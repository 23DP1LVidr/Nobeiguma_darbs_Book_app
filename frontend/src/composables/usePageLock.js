import { onBeforeUnmount, onMounted } from "vue"

const LOCK_CLASS = "app-page-locked"
const MOBILE_LOCK_CLASS = "app-page-lock-mobile"

let activeLocks = 0
let activeMobileLocks = 0

export function usePageLock({ lockMobile = false } = {}) {
  let active = false

  onMounted(() => {
    if (active) return

    active = true
    activeLocks += 1
    if (lockMobile) activeMobileLocks += 1
    syncPageLockClasses()
  })

  onBeforeUnmount(() => {
    if (!active) return

    active = false
    activeLocks = Math.max(activeLocks - 1, 0)
    if (lockMobile) activeMobileLocks = Math.max(activeMobileLocks - 1, 0)
    syncPageLockClasses()
  })
}

function syncPageLockClasses() {
  if (typeof document === "undefined") return

  const shouldLock = activeLocks > 0
  const shouldLockMobile = activeMobileLocks > 0

  document.documentElement.classList.toggle(LOCK_CLASS, shouldLock)
  document.body.classList.toggle(LOCK_CLASS, shouldLock)
  document.documentElement.classList.toggle(MOBILE_LOCK_CLASS, shouldLockMobile)
  document.body.classList.toggle(MOBILE_LOCK_CLASS, shouldLockMobile)
}
