import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/home',
      name: 'home',
      component: HomeView,
    },
    {
      path: '/main',
      name: 'main',
      component: () => import('../views/MainView.vue'),
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
      path: "/friends",
      name: 'friends',
      component: () => import('../views/FriendsView.vue'),
    },
    {
      path: "/messages",
      name: 'messages',
      component: () => import('../views/MessagesView.vue'),
    },
    {
      path: '/about',
      name: 'about',
      component: () => import('../views/AboutView.vue'),
    },
    {
      path: '/register',
      name: 'register',
      component: () => import('../views/Register.vue'),
    },
    {
      path: '/login',
      name: 'login',
      component: () => import('../views/LoginView.vue'),
    },
    {
      path: '/profile',
      name: 'profile',
      component: () => import('../views/ProfileView.vue'),
    },
    {
      path: '/settings',
      name: 'settings',
      component: () => import('../views/SettingsView.vue'),
    },
  ],
})

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem("token")

  const publicPages = ["/", "/login", "/register"]
  const authRequired = !publicPages.includes(to.path)

  if (authRequired && !token) {
    next("/login")
  } else {
    next()
  }
})

export default router