import { onBeforeUnmount, onMounted, unref } from "vue"

const DEFAULT_IGNORE_SELECTOR = [
  ".v-overlay",
  ".v-dialog",
  ".v-menu",
  ".v-snackbar",
  "input",
  "textarea",
  "select",
  "[contenteditable='true']",
].join(", ")

export function useScrollForwarding({
  source,
  target,
  disabledBelow = 960,
  ignoreSelector = DEFAULT_IGNORE_SELECTOR,
}) {
  function handleWheel(event) {
    if (window.innerWidth < disabledBelow) return

    const targetElement = resolveElement(target)
    if (!targetElement || shouldUseNativeScroll(event.target, targetElement, ignoreSelector)) {
      return
    }

    event.preventDefault()
    targetElement.scrollTop += event.deltaY
  }

  onMounted(() => {
    resolveElement(source)?.addEventListener("wheel", handleWheel, {
      capture: true,
      passive: false,
    })
  })

  onBeforeUnmount(() => {
    resolveElement(source)?.removeEventListener("wheel", handleWheel, {
      capture: true,
    })
  })
}

function resolveElement(value) {
  const resolved = unref(value)
  return resolved?.$el ?? resolved
}

function shouldUseNativeScroll(target, scrollTarget, ignoreSelector) {
  if (!(target instanceof Element)) return false
  if (target.closest(ignoreSelector)) return true

  return scrollTarget.contains(target)
}
