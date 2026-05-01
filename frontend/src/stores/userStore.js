import { computed, reactive } from "vue"

const savedUser = JSON.parse(localStorage.getItem("user")) || null

export const userState = reactive({
  user: savedUser,
  avatar: localStorage.getItem("profile_avatar") || "",
  city: localStorage.getItem("profile_city") || "",
  about: localStorage.getItem("profile_about") || "",
})

export const initials = computed(() => {
  const nameInitial = userState.user?.name?.[0] || ""
  const surnameInitial = userState.user?.surname?.[0] || ""
  return `${nameInitial}${surnameInitial}`.toUpperCase() || "U"
})

export function updateUser(data) {
  userState.user = {
    ...userState.user,
    ...data,
  }

  localStorage.setItem("user", JSON.stringify(userState.user))
}

export function updateProfile(data) {
  if (data.avatar !== undefined) {
    userState.avatar = data.avatar
    localStorage.setItem("profile_avatar", data.avatar)
  }

  if (data.city !== undefined) {
    userState.city = data.city
    localStorage.setItem("profile_city", data.city)
  }

  if (data.about !== undefined) {
    userState.about = data.about
    localStorage.setItem("profile_about", data.about)
  }
}

export function logout(router) {
  localStorage.removeItem("token")
  localStorage.removeItem("user")
  localStorage.removeItem("profile_avatar")
  localStorage.removeItem("profile_city")
  localStorage.removeItem("profile_about")

  userState.user = null
  userState.avatar = ""
  userState.city = ""
  userState.about = ""

  router.replace("/login")
}

export function setLoggedInUser(user) {
  userState.user = user
  localStorage.setItem("user", JSON.stringify(user))
}