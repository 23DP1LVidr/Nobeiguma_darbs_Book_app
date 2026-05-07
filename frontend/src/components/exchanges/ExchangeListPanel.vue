<template>
  <div class="d-flex justify-space-between align-center mb-6">
    <div>
      <h1 class="text-h4 font-weight-bold">Manas apmaiņas</h1>
      <p class="text-body-1 text-medium-emphasis">
        Sekojiet līdzi savām grāmatu apmaiņām
      </p>
    </div>

    <v-btn color="primary" prepend-icon="mdi-magnify" to="/main">
      Atrast grāmatas
    </v-btn>
  </div>

  <v-tabs v-model="activeTab" color="primary" class="mb-4">
    <v-tab value="pending">Gaida <span class="ml-2">({{ pendingExchanges.length }})</span></v-tab>
    <v-tab value="active">Aktīvas <span class="ml-2">({{ activeExchanges.length }})</span></v-tab>
    <v-tab value="completed">Pabeigtas <span class="ml-2">({{ completedExchanges.length }})</span></v-tab>
  </v-tabs>

  <div v-if="loading" class="text-center py-10">
    <v-progress-circular indeterminate color="primary" />
  </div>

  <v-window v-else v-model="activeTab">
    <v-window-item value="pending">
      <div v-if="pendingExchanges.length === 0" class="text-center py-10">
        <v-icon size="56" color="grey">mdi-swap-horizontal</v-icon>
        <div class="text-h6 mt-3">Nav gaidošu apmaiņu</div>
      </div>

      <ExchangeCard
        v-for="exchange in pendingExchanges"
        :key="exchange.id"
        :exchange="exchange"
        :current-user-id="currentUserId"
        pending
        @accept="$emit('accept', $event)"
        @reject="$emit('reject', $event)"
        @cancel="$emit('cancel', $event)"
      />
    </v-window-item>

    <v-window-item value="active">
      <div v-if="activeExchanges.length === 0" class="text-center py-10">
        <v-icon size="56" color="grey">mdi-handshake</v-icon>
        <div class="text-h6 mt-3">Nav aktīvu apmaiņu</div>
      </div>

      <ExchangeCard
        v-for="exchange in activeExchanges"
        :key="exchange.id"
        :exchange="exchange"
        :current-user-id="currentUserId"
        active
        @complete="$emit('complete', $event)"
      />
    </v-window-item>

    <v-window-item value="completed">
      <div v-if="completedExchanges.length === 0" class="text-center py-10">
        <v-icon size="56" color="grey">mdi-check-circle-outline</v-icon>
        <div class="text-h6 mt-3">Nav pabeigtu apmaiņu</div>
      </div>

      <ExchangeCard
        v-for="exchange in completedExchanges"
        :key="exchange.id"
        :exchange="exchange"
        :current-user-id="currentUserId"
        completed
      />
    </v-window-item>
  </v-window>
</template>

<script setup>
import { ref } from "vue"
import ExchangeCard from "@/components/ExchangeCard.vue"

defineProps({
  pendingExchanges: {
    type: Array,
    default: () => [],
  },
  activeExchanges: {
    type: Array,
    default: () => [],
  },
  completedExchanges: {
    type: Array,
    default: () => [],
  },
  currentUserId: {
    type: Number,
    required: true,
  },
  loading: {
    type: Boolean,
    default: false,
  },
})

defineEmits(["accept", "reject", "cancel", "complete"])

const activeTab = ref("pending")
</script>
