#!/usr/bin/env bash
# Usage: ./fix-modules-structure.sh [--dry-run]
# - Loops through Modules/*
# - Renames controllers->Controllers, models->Models
# - Creates Resources per module
# - Moves views -> Resources/views (preserves subfolders)
# Safe on case-insensitive FS via temp hop. Idempotent.

set -euo pipefail

DRY_RUN=0
[[ "${1:-}" == "--dry-run" ]] && DRY_RUN=1

ROOT="${ROOT:-$(pwd)}"
MODULES_DIR="${MODULES_DIR:-${ROOT}/Modules}"

log() { printf '%s\n' "$*" >&2; }
run() { if [[ "$DRY_RUN" -eq 1 ]]; then log "[dry] $*"; else eval "$@"; fi; }

exists_dir_ci() {
  # case-insensitive dir existence: prints first match path or empty
  local base="$1" ; local name="$2"
  shopt -s nullglob nocaseglob
  local matches=( "$base"/"$name" )
  shopt -u nullglob nocaseglob
  if (( ${#matches[@]} )); then printf '%s\n' "${matches[0]}"; else printf '' ; fi
}

safe_mv_dir() {
  # mv that survives case-only renames on case-insensitive FS
  local src="$1" dst="$2"
  [[ "$src" == "$dst" ]] && return 0
  if [[ "${src,,}" == "${dst,,}" ]]; then
    local tmp="${src}.renaming.$RANDOM.$RANDOM"
    run "mv -f \"$src\" \"$tmp\""
    run "mv -f \"$tmp\" \"$dst\""
  else
    run "mv -f \"$src\" \"$dst\""
  fi
}

copy_tree_into() {
  # copy contents of $src into $dst (create $dst), then remove empty $src
  local src="$1" dst="$2"
  [[ -d "$src" ]] || return 0
  run "mkdir -p \"$dst\""
  # rsync preferred for preserving tree; fallback to cp if unavailable
  if command -v rsync >/dev/null 2>&1; then
    run "rsync -a \"$src\"/ \"$dst\"/"
  else
    run "cp -R \"$src\"/. \"$dst\"/"
  fi
}

move_views_into_resources() {
  local module_path="$1"
  local resources_path="$module_path/Resources"
  local target_views="$resources_path/views"

  # Detect existing views dirs with various casings/locations
  local candidates=()
  for rel in "views" "Views" "resources/views" "Resources/views"; do
    local p
    p="$(exists_dir_ci "$module_path" "$rel")" || true
    [[ -n "$p" ]] && candidates+=( "$p" )
  done

  [[ ${#candidates[@]} -eq 0 ]] && return 0

  run "mkdir -p \"$target_views\""

  for src in "${candidates[@]}"; do
    if [[ "$src" == "$target_views" ]]; then
      continue
    fi
    # Move contents into Resources/views (preserve structure)
    copy_tree_into "$src" "$target_views"
    # Remove the original dir if it's now empty
    if [[ "$DRY_RUN" -eq 0 ]] && [[ -d "$src" ]]; then
      # try to remove; ignore if not empty
      rmdir "$src" 2>/dev/null || true
    fi
  done
}

rename_known_dirs() {
  local module_path="$1"

  # controllers -> Controllers
  local ctrl_src; ctrl_src="$(exists_dir_ci "$module_path" "controllers")" || true
  if [[ -n "$ctrl_src" ]]; then
    local ctrl_dst="$module_path/Controllers"
    if [[ "${ctrl_src,,}" != "${ctrl_dst,,}" ]]; then
      log "Dir  : $ctrl_src -> $ctrl_dst"
      safe_mv_dir "$ctrl_src" "$ctrl_dst"
    fi
  fi

  # models -> Models
  local models_src; models_src="$(exists_dir_ci "$module_path" "models")" || true
  if [[ -n "$models_src" ]]; then
    local models_dst="$module_path/Models"
    if [[ "${models_src,,}" != "${models_dst,,}" ]]; then
      log "Dir  : $models_src -> $models_dst"
      safe_mv_dir "$models_src" "$models_dst"
    fi
  fi

  # Ensure Resources exists
  local resources_dst="$module_path/Resources"
  if [[ ! -d "$resources_dst" ]]; then
    log "Dir  : create $resources_dst"
    run "mkdir -p \"$resources_dst\""
  fi

  # Move any views -> Resources/views
  move_views_into_resources "$module_path"
}

main() {
  if [[ ! -d "$MODULES_DIR" ]]; then
    log "Modules dir not found: $MODULES_DIR"
    exit 1
  fi

  shopt -s nullglob
  local modules=( "$MODULES_DIR"/* )
  shopt -u nullglob

  if (( ${#modules[@]} == 0 )); then
    log "No modules in $MODULES_DIR"
    exit 0
  fi

  for module_path in "${modules[@]}"; do
    [[ -d "$module_path" ]] || continue
    log "==> Module: $(basename "$module_path")"
    rename_known_dirs "$module_path"
  done

  log "Done."
  log "Next steps:"
  log "  - php artisan modules:step3:namespace-methods   # inject namespaces + camelCase methods (PSR-12 names)"
  log "  - php artisan modules:step4:rewrite-calls       # update call-sites to new method names"
  log "  - php artisan modules:step5:bladeify            # convert PHP views → Blade"
  log "  - php artisan modules:step6:routes              # generate Modules/{Module}/Routes/{module}.php"
  log "  - composer rector && composer lint              # type hints/returns + PSR-12 formatting"
}

main "$@"
