<template>
  <v-card rounded="xl" elevation="2" class="pa-4 mb-4 content-card">
    <div class="d-flex justify-space-between align-center mb-3">
      <div class="d-flex align-center">
        <v-avatar size="40" :color="statusColor" class="mr-3">
          <v-icon color="white">
            {{ pending ? "mdi-clock-outline" : active ? "mdi-handshake" : "mdi-check" }}
          </v-icon>
        </v-avatar>

        <div>
          <div class="text-subtitle-1 font-weight-bold">
            {{ isIncoming ? "Apmaiņas pieprasījums" : "Jūsu piedāvājums" }}
          </div>
          <div class="text-caption text-medium-emphasis">
            {{ isIncoming ? "Saņemts no cita lietotāja" : "Nosūtīts citam lietotājam" }}
          </div>
        </div>
      </div>

      <v-chip :color="statusColor" variant="tonal">
        {{ statusText }}
      </v-chip>
    </div>

    <v-divider class="mb-3" />

    <div class="exchange-books">
      <div class="d-flex align-center">
        <v-img
          v-if="leftBook?.image"
          :src="leftBook.image"
          width="60"
          height="90"
          class="rounded-lg mr-3"
          cover
        />

        <div v-else class="book-placeholder mr-3">
          <v-icon>mdi-book-open-page-variant</v-icon>
        </div>

        <div>
          <div class="text-body-2 font-weight-bold">
            {{ isIncoming ? "Jūsu grāmata:" : "Jūs piedāvājat:" }}
          </div>
          <div class="text-body-1">{{ leftBook?.title || "Nav izvēlēta" }}</div>
          <div class="text-caption text-medium-emphasis">{{ leftBook?.author }}</div>
        </div>
      </div>

      <v-icon size="32" color="primary">mdi-arrow-left-right</v-icon>

      <div class="d-flex align-center">
        <div class="text-right mr-3">
          <div class="text-body-2 font-weight-bold">
            {{ isIncoming ? "Jums piedāvā:" : "Jūs vēlaties:" }}
          </div>
          <div class="text-body-1">{{ rightBook?.title || "Nav izvēlēta" }}</div>
          <div class="text-caption text-medium-emphasis">{{ rightBook?.author }}</div>
        </div>

        <v-img
          v-if="rightBook?.image"
          :src="rightBook.image"
          width="60"
          height="90"
          class="rounded-lg"
          cover
        />

        <div v-else class="book-placeholder">
          <v-icon>mdi-book-open-page-variant</v-icon>
        </div>
      </div>
    </div>

    <v-divider class="my-3" />

    <div class="d-flex align-center">
      <v-avatar size="28" color="primary" class="mr-2">
        <v-img v-if="otherUser?.avatar" :src="otherUser.avatar" />
        <span v-else class="text-white text-caption">{{ initialsFor(otherUser) }}</span>
      </v-avatar>

      <span class="text-body-2">
        {{ otherUser?.name }} {{ otherUser?.surname }}
      </span>

      <v-spacer />

      <template v-if="pending && isIncoming">
        <v-btn variant="outlined" size="small" class="mr-2" @click="$emit('reject', exchange.id)">
          Noraidīt
        </v-btn>
        <v-btn color="primary" size="small" @click="$emit('accept', exchange.id)">
          Apstiprināt
        </v-btn>
      </template>

      <template v-else-if="pending && !isIncoming">
        <v-btn variant="text" size="small" color="error" @click="$emit('cancel', exchange.id)">
          Atcelt
        </v-btn>
      </template>

      <template v-else-if="active">
        <v-btn size="small" color="primary" variant="tonal" @click="$emit('complete', exchange.id)">
          Pabeigt
        </v-btn>
      </template>
    </div>
  </v-card>
</template>

<script setup>
import { computed } from "vue"

const props = defineProps({
  exchange: Object,
  currentUserId: [Number, String],
  pending: Boolean,
  active: Boolean,
  completed: Boolean,
})

const emit = defineEmits(["accept", "reject", "cancel", "complete"])

const currentUserId = computed(() => Number(props.currentUserId))
const exchangeOwnerId = computed(() => Number(props.exchange.owner_id))

const isIncoming = computed(() => exchangeOwnerId.value === currentUserId.value)

const requester = computed(() => props.exchange.requester || null)
const owner = computed(() => props.exchange.owner || null)
const requestedBook = computed(() => props.exchange.requestedBook || props.exchange.requested_book || null)
const offeredBook = computed(() => props.exchange.offeredBook || props.exchange.offered_book || null)

const otherUser = computed(() => {
  if (isIncoming.value) {
    return requester.value || owner.value
  }

  return owner.value || requester.value
})

const leftBook = computed(() => {
  if (isIncoming.value) return requestedBook.value
  return offeredBook.value || requestedBook.value
})

const rightBook = computed(() => {
  if (isIncoming.value) return offeredBook.value
  return requestedBook.value
})

const statusColor = computed(() => {
  if (props.exchange.status === "pending") return "warning"
  if (props.exchange.status === "accepted") return "success"
  if (props.exchange.status === "completed") return "primary"
  return "grey"
})

const statusText = computed(() => {
  if (props.exchange.status === "pending") return "Gaida"
  if (props.exchange.status === "accepted") return "Aktīva"
  if (props.exchange.status === "completed") return "Pabeigta"
  return props.exchange.status
})

function initialsFor(user) {
  return `${user?.name?.[0] || ""}${user?.surname?.[0] || ""}`.toUpperCase() || "U"
}
</script>

<style scoped>
.exchange-books {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
}

.book-placeholder {
  width: 60px;
  height: 90px;
  border-radius: 8px;
  background: rgba(var(--v-theme-on-surface), 0.06);
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>
