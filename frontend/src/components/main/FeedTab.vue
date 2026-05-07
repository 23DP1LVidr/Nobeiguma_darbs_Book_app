<template>
  <v-card rounded="xl" elevation="0" class="pa-4 mb-4 content-card">
    <div class="d-flex align-center">
      <v-avatar size="40" color="primary" class="mr-3">
        <v-img v-if="user?.avatar" :src="user.avatar" />
        <span v-else class="text-white">{{ initials }}</span>
      </v-avatar>

      <v-text-field
        v-model="newPost"
        placeholder="Ko tu domā par grāmatām?"
        variant="outlined"
        density="compact"
        hide-details
        class="flex-grow-1 search-field"
        :disabled="creating"
        @keyup.enter="submitPost"
      />
    </div>

    <v-img
      v-if="imagePreview"
      :src="imagePreview"
      max-height="220"
      class="post-image rounded-lg mt-3"
      contain
    />

    <div class="d-flex justify-end mt-3 gap-2">
      <v-file-input
        v-model="imageFile"
        accept="image/*"
        class="d-none"
        hide-details
      />

      <v-btn variant="tonal" size="small" prepend-icon="mdi-image" @click="openImagePicker">
        Bilde
      </v-btn>

      <v-btn
        v-if="imagePreview"
        variant="text"
        size="small"
        color="error"
        @click="clearImage"
      >
        Noņemt
      </v-btn>

      <v-btn color="primary" size="small" :loading="creating" @click="submitPost">
        Publicēt
      </v-btn>
    </div>
  </v-card>

  <div v-if="loading" class="text-center py-10">
    <v-progress-circular indeterminate color="primary" />
  </div>

  <div v-else-if="posts.length === 0" class="text-center py-10">
    <v-icon size="56" color="grey">mdi-post-outline</v-icon>
    <div class="text-h6 mt-3">Vēl nav ierakstu</div>
    <div class="text-body-2 text-medium-emphasis">
      Publicē pirmo domu par grāmatām.
    </div>
  </div>

  <v-card
    v-for="post in posts"
    :key="post.id"
    rounded="xl"
    elevation="0"
    class="pa-4 mb-4 content-card"
  >
    <div class="d-flex align-center mb-3">
      <router-link :to="profileLink(post.user)" class="profile-link mr-3">
        <v-avatar size="40" color="primary">
          <v-img v-if="post.user?.avatar" :src="post.user.avatar" />
          <span v-else class="text-white">{{ getUserInitials(post.user) }}</span>
        </v-avatar>
      </router-link>

      <router-link :to="profileLink(post.user)" class="profile-link">
        <div class="text-subtitle-2 font-weight-bold">
          {{ post.user?.name }} {{ post.user?.surname }}
        </div>
        <div class="text-caption text-medium-emphasis">
          {{ formatDate(post.created_at) }}
        </div>
      </router-link>

      <v-spacer />

      <v-menu
        v-if="canDeletePost(post)"
        location="bottom right"
        transition="scale-transition"
        offset-y
      >
        <template #activator="{ props }">
          <v-btn icon size="small" v-bind="props">
            <v-icon>mdi-dots-vertical</v-icon>
          </v-btn>
        </template>

        <v-list class="feed-delete-menu" density="compact">
          <v-list-item @click="deletePost(post)" class="feed-delete-menu-item">
            <template #prepend>
              <v-icon color="error">mdi-delete</v-icon>
            </template>
            <v-list-item-title>Dzēst</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>
    </div>

    <p class="text-body-1 mb-0 preserve-lines">
      {{ post.content }}
    </p>

    <v-img
      v-if="post.image"
      :src="post.image"
      max-height="520"
      class="post-image rounded-lg mt-3"
      contain
    />

    <div class="d-flex gap-4 mt-3 align-center">
      <v-btn
        variant="text"
        size="small"
        :color="post.liked_by_current_user ? 'error' : undefined"
        :prepend-icon="post.liked_by_current_user ? 'mdi-heart' : 'mdi-heart-outline'"
        @click="$emit('toggle-like', post)"
      >
        Patīk ({{ post.likes_count || 0 }})
      </v-btn>

      <v-btn
        variant="text"
        size="small"
        prepend-icon="mdi-comment-outline"
        @click="toggleComments(post.id)"
      >
        Komentāri ({{ post.comments_count || post.comments?.length || 0 }})
      </v-btn>
    </div>

    <div v-if="openComments[post.id]" class="mt-3">
      <div v-if="post.comments?.length" class="comments">
        <div
          v-for="comment in post.comments"
          :key="comment.id"
          class="comment-row"
        >
          <router-link :to="profileLink(comment.user)" class="profile-link mr-2">
            <v-avatar size="28" color="primary">
              <v-img v-if="comment.user?.avatar" :src="comment.user.avatar" />
              <span v-else class="text-white text-caption">{{ getUserInitials(comment.user) }}</span>
            </v-avatar>
          </router-link>

          <div class="comment-bubble">
            <router-link :to="profileLink(comment.user)" class="profile-link text-caption font-weight-bold">
              {{ comment.user?.name }} {{ comment.user?.surname }}
            </router-link>
            <div class="text-body-2 preserve-lines">
              {{ comment.content }}
            </div>
          </div>

          <v-menu
            v-if="canDeleteComment(comment)"
            location="bottom right"
            transition="scale-transition"
            offset-y
          >
            <template #activator="{ props }">
              <v-btn icon size="small" v-bind="props" class="ml-auto">
                <v-icon>mdi-dots-vertical</v-icon>
              </v-btn>
            </template>

            <v-list class="feed-delete-menu" density="compact">
              <v-list-item @click="deleteComment(post, comment)" class="feed-delete-menu-item">
                <template #prepend>
                  <v-icon color="error">mdi-delete</v-icon>
                </template>
                <v-list-item-title>Dzēst</v-list-item-title>
              </v-list-item>
            </v-list>
          </v-menu>
        </div>
      </div>

      <div v-else class="text-body-2 text-medium-emphasis">
        Vēl nav komentāru.
      </div>

      <div class="d-flex align-center mt-3">
        <v-avatar size="28" color="primary" class="mr-2">
          <v-img v-if="user?.avatar" :src="user.avatar" />
          <span v-else class="text-white text-caption">{{ initials }}</span>
        </v-avatar>

        <v-text-field
          v-model="commentDrafts[post.id]"
          placeholder="Rakstīt komentāru..."
          variant="outlined"
          density="compact"
          hide-details
          class="search-field"
          @keyup.enter="submitComment(post)"
        />

        <v-btn
          icon
          color="primary"
          class="ml-2"
          :disabled="!commentDrafts[post.id]?.trim()"
          @click="submitComment(post)"
        >
          <v-icon>mdi-send</v-icon>
        </v-btn>
      </div>
    </div>
  </v-card>
</template>

<script setup>
import { ref, watch } from "vue"

const props = defineProps({
  user: {
    type: Object,
    default: null,
  },
  initials: {
    type: String,
    required: true,
  },
  posts: {
    type: Array,
    default: () => [],
  },
  loading: {
    type: Boolean,
    default: false,
  },
  creating: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(["create", "toggle-like", "comment", "delete-post", "delete-comment"])

const newPost = ref("")
const imageFile = ref(null)
const imagePreview = ref(null)
const commentDrafts = ref({})
const openComments = ref({})

function submitPost() {
  const content = newPost.value.trim()

  if (!content || props.creating) return

  emit("create", {
    content,
    image: imagePreview.value,
  })
  newPost.value = ""
  clearImage()
}

watch(imageFile, (file) => {
  if (!file) return

  const selected = Array.isArray(file) ? file[0] : file
  if (!selected) return

  const reader = new FileReader()

  reader.onload = (event) => {
    imagePreview.value = event.target.result
  }

  reader.readAsDataURL(selected)
})

function openImagePicker() {
  const input = document.querySelector(".v-file-input input[type='file']")
  input?.click()
}

function clearImage() {
  imageFile.value = null
  imagePreview.value = null
}

function toggleComments(postId) {
  openComments.value[postId] = !openComments.value[postId]
}

function submitComment(post) {
  const content = commentDrafts.value[post.id]?.trim()

  if (!content) return

  emit("comment", {
    post,
    content,
  })

  commentDrafts.value[post.id] = ""
}

function deletePost(post) {
  emit("delete-post", post)
}

function deleteComment(post, comment) {
  emit("delete-comment", { post, comment })
}

function canDeletePost(post) {
  return props.user?.role === "admin" || post.user_id === props.user?.id
}

function canDeleteComment(comment) {
  return props.user?.role === "admin" || comment.user_id === props.user?.id
}

function profileLink(user) {
  if (!user?.id) return "/main"
  return Number(user.id) === Number(props.user?.id) ? "/profile" : `/profile/${user.id}`
}

function getUserInitials(user) {
  return `${user?.name?.[0] || ""}${user?.surname?.[0] || ""}`.toUpperCase() || "U"
}

function formatDate(value) {
  if (!value) return ""

  return new Intl.DateTimeFormat("lv-LV", {
    day: "2-digit",
    month: "short",
    year: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  }).format(new Date(value))
}
</script>

<style scoped>
.content-card {
  background: rgb(var(--v-theme-surface)) !important;
  color: rgb(var(--v-theme-on-surface)) !important;
  border: 1px solid rgba(var(--v-theme-on-surface), 0.08);
}

.search-field :deep(.v-field) {
  background: rgba(var(--v-theme-on-surface), 0.05);
}

.preserve-lines {
  white-space: pre-line;
}

.post-image {
  background: rgba(var(--v-theme-on-surface), 0.05);
}

.profile-link {
  color: inherit;
  cursor: pointer;
  text-decoration: none;
}

.profile-link:hover {
  text-decoration: underline;
}

.profile-link:has(.v-avatar):hover {
  text-decoration: none;
}

.comments {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.comment-row {
  display: flex;
  align-items: flex-start;
}

.comment-bubble {
  min-width: 0;
  padding: 8px 12px;
  border-radius: 12px;
  background: rgba(var(--v-theme-on-surface), 0.06);
}

.feed-delete-menu {
  min-width: 160px;
}

.feed-delete-menu-item {
  min-height: 44px;
}

.feed-delete-menu-item :deep(.v-list-item__prepend) {
  align-items: center;
  align-self: center;
  margin-inline-end: 12px;
}

.feed-delete-menu-item :deep(.v-list-item-title) {
  line-height: 1;
}

.gap-2 {
  gap: 8px;
}

.gap-4 {
  gap: 16px;
}
</style>
