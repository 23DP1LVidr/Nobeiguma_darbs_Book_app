<template>
  <div class="admin-page">
    <v-navigation-drawer permanent width="240" class="admin-drawer">
      <div class="pa-4">
        <div class="text-h6 font-weight-bold">Admin</div>
        <div class="text-caption text-medium-emphasis">
          {{ user?.name }} {{ user?.surname }}
        </div>
      </div>

      <v-divider />

      <v-list v-model:selected="selectedNav" density="compact" nav>
        <v-list-item prepend-icon="mdi-view-dashboard" title="Dashboard" value="dashboard" />
        <v-list-item prepend-icon="mdi-account-group" title="Users" value="users" />
        <v-list-item prepend-icon="mdi-book-open-variant" title="Books" value="books" />
        <v-list-item prepend-icon="mdi-swap-horizontal" title="Exchanges" value="exchanges" />
      </v-list>
    </v-navigation-drawer>

    <main class="admin-main">
      <div class="d-flex align-center mb-6">
        <div>
          <div class="text-h4 font-weight-bold">Admin Panel</div>
          <div class="text-body-2 text-medium-emphasis">Manage users, books, and exchanges.</div>
        </div>

        <v-spacer />

        <v-btn variant="tonal" prepend-icon="mdi-home" to="/main">
          Back to app
        </v-btn>
      </div>

      <v-alert v-if="error" type="error" variant="tonal" class="mb-4">
        {{ error }}
      </v-alert>

      <div v-if="activeTab === 'dashboard'" class="dashboard-grid">
        <v-card class="pa-4 admin-card" elevation="0">
          <div class="text-caption text-medium-emphasis">Total users</div>
          <div class="text-h4 font-weight-bold">{{ users.length }}</div>
        </v-card>

        <v-card class="pa-4 admin-card" elevation="0">
          <div class="text-caption text-medium-emphasis">Total books</div>
          <div class="text-h4 font-weight-bold">{{ books.length }}</div>
        </v-card>

        <v-card class="pa-4 admin-card" elevation="0">
          <div class="text-caption text-medium-emphasis">Total exchanges</div>
          <div class="text-h4 font-weight-bold">{{ exchanges.length }}</div>
        </v-card>
      </div>

      <v-card v-if="activeTab === 'users'" class="admin-card" elevation="0">
        <v-card-title>Users</v-card-title>

        <v-table>
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Role</th>
              <th class="text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in users" :key="item.id">
              <td>{{ item.name }} {{ item.surname }}</td>
              <td>{{ item.email }}</td>
              <td>{{ item.role }}</td>
              <td class="text-right">
                <v-btn
                  icon="mdi-delete"
                  size="small"
                  color="error"
                  variant="text"
                  :disabled="item.id === user?.id"
                  @click="deleteUser(item)"
                />
              </td>
            </tr>
          </tbody>
        </v-table>
      </v-card>

      <v-card v-if="activeTab === 'books'" class="admin-card" elevation="0">
        <v-card-title>Books</v-card-title>

        <v-table>
          <thead>
            <tr>
              <th>Title</th>
              <th>Author</th>
              <th>Owner</th>
              <th class="text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in books" :key="item.id">
              <td>{{ item.title }}</td>
              <td>{{ item.author }}</td>
              <td>{{ fullName(item.user) }}</td>
              <td class="text-right">
                <v-btn
                  icon="mdi-delete"
                  size="small"
                  color="error"
                  variant="text"
                  @click="deleteBook(item)"
                />
              </td>
            </tr>
          </tbody>
        </v-table>
      </v-card>

      <v-card v-if="activeTab === 'exchanges'" class="admin-card" elevation="0">
        <v-card-title>Exchanges</v-card-title>

        <v-list lines="two" bg-color="transparent">
          <v-list-item v-for="item in exchanges" :key="item.id">
            <v-list-item-title>
              {{ fullName(item.requester) }} -> {{ fullName(item.owner) }}
            </v-list-item-title>

            <v-list-item-subtitle>
              {{ item.requested_book?.title || "Book" }}
              <span v-if="item.offered_book"> for {{ item.offered_book.title }}</span>
            </v-list-item-subtitle>

            <template #append>
              <v-chip size="small" color="primary" variant="tonal">
                {{ item.status }}
              </v-chip>
            </template>
          </v-list-item>
        </v-list>
      </v-card>
    </main>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from "vue"
import axios from "axios"

const API_URL = "http://127.0.0.1:8000/api"

const user = ref(JSON.parse(localStorage.getItem("user")) || null)
const selectedNav = ref(["dashboard"])
const users = ref([])
const books = ref([])
const exchanges = ref([])
const error = ref("")

const activeTab = computed(() => selectedNav.value[0] || "dashboard")

function authHeaders() {
  return {
    Authorization: `Bearer ${localStorage.getItem("token")}`,
  }
}

function fullName(value) {
  if (!value) return "-"
  return `${value.name || ""} ${value.surname || ""}`.trim() || value.email || "-"
}

async function fetchAdminData() {
  error.value = ""

  try {
    const [usersResponse, booksResponse, exchangesResponse] = await Promise.all([
      axios.get(`${API_URL}/admin/users`, { headers: authHeaders() }),
      axios.get(`${API_URL}/admin/books`, { headers: authHeaders() }),
      axios.get(`${API_URL}/admin/exchanges`, { headers: authHeaders() }),
    ])

    users.value = usersResponse.data
    books.value = booksResponse.data
    exchanges.value = exchangesResponse.data
  } catch (err) {
    error.value = err.response?.data?.message || "Failed to load admin data."
  }
}

async function deleteUser(item) {
  if (!window.confirm(`Delete user ${fullName(item)}?`)) return

  try {
    await axios.delete(`${API_URL}/admin/users/${item.id}`, { headers: authHeaders() })
    users.value = users.value.filter((userItem) => userItem.id !== item.id)
  } catch (err) {
    error.value = err.response?.data?.message || "Failed to delete user."
  }
}

async function deleteBook(item) {
  if (!window.confirm(`Delete book "${item.title}"?`)) return

  try {
    await axios.delete(`${API_URL}/admin/books/${item.id}`, { headers: authHeaders() })
    books.value = books.value.filter((book) => book.id !== item.id)
  } catch (err) {
    error.value = err.response?.data?.message || "Failed to delete book."
  }
}

onMounted(fetchAdminData)
</script>

<style scoped>
.admin-page {
  min-height: 100vh;
  background: rgb(var(--v-theme-background));
  color: rgb(var(--v-theme-on-background));
}

.admin-drawer {
  background: rgb(var(--v-theme-surface)) !important;
  border-right: 1px solid rgba(var(--v-theme-on-surface), 0.08);
}

.admin-main {
  margin-left: 240px;
  padding: 32px;
}

.admin-card {
  background: rgb(var(--v-theme-surface)) !important;
  color: rgb(var(--v-theme-on-surface)) !important;
  border: 1px solid rgba(var(--v-theme-on-surface), 0.08);
}

.dashboard-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 16px;
}

@media (max-width: 959px) {
  .admin-main {
    margin-left: 0;
    padding: 16px;
  }

  .dashboard-grid {
    grid-template-columns: 1fr;
  }
}
</style>
