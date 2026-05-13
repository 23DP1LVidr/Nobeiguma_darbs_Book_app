import { createRouter, createWebHistory } from 'vue-router'
import axios from 'axios'
import { API_URL } from '@/services/api'
import HomeView from '../views/HomeView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/home',
      name: 'home',
      component: HomeView,
      meta: { public: true },
    },
    {
      path: '/main',
      name: 'main',
      component: () => import('../views/MainView.vue'),
      meta: { public: true },
    },
    {
      path: '/library',
      name: 'library',
      component: () => import('../views/LibraryView.vue'),
    },
    {
      path: '/exchanges',
      name: 'exchanges',
      component: () => import('../views/ExchangesView.vue'),
    },
    {
      path: "/messages",
      name: 'messages',
      component: () => import('../views/MessagesView.vue'),
    },
    {
      path: '/register',
      name: 'register',
      component: () => import('../views/Register.vue'),
      meta: { public: true },
    },
    {
      path: '/login',
      name: 'login',
      component: () => import('../views/LoginView.vue'),
      meta: { public: true },
    },
    {
      path: '/profile',
      name: 'profile',
      component: () => import('../views/ProfileView.vue'),
    },
    {
      path: '/profile/:id',
      name: 'UserProfile',
      component: () => import('@/views/UserProfileView.vue'),
      meta: { public: true },
    },
    {
      path: '/settings',
      name: 'settings',
      component: () => import('../views/SettingsView.vue'),
    },
    {
      path: '/admin',
      name: 'admin',
      component: () => import('../views/AdminView.vue'),
      meta: {
        requiresAdmin: true,
      },
    },
  ],
})

async function fetchCurrentUser(token) {
  const response = await axios.get(`${API_URL}/me`, {
    headers: {
      Authorization: `Bearer ${token}`,
    },
  })

  localStorage.setItem("user", JSON.stringify(response.data.user))
  return response.data.user
}

router.beforeEach(async (to, from, next) => {
  const token = localStorage.getItem("token")
  let user = JSON.parse(localStorage.getItem("user") || "null")

  const publicPages = ["/", "/login", "/register"]
  const authRequired = !to.meta.public && !publicPages.includes(to.path)

  if (authRequired && !token) {
    next("/register")
    return
  }

  if (to.meta.requiresAdmin) {
    try {
      user = await fetchCurrentUser(token)
    } catch (error) {
      next("/login")
      return
    }

    if (user?.role !== "admin") {
      next("/main")
      return
    }
  }

  next()
})

export default router
