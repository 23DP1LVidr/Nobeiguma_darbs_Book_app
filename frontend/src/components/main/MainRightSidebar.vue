<template>
  <v-col cols="12" md="3" class="d-none d-md-block">
    <v-card rounded="xl" elevation="0" class="pa-4 mb-4 panel-card">
      <div class="text-subtitle-1 font-weight-bold mb-3">Populāras šonedēļ</div>

      <div v-for="item in trendingBooks" :key="item.title" class="d-flex align-center mb-3">
        <v-img
          :src="item.image"
          width="50"
          height="70"
          class="rounded-lg mr-3"
          cover
        />

        <div>
          <div class="text-body-2 font-weight-bold">{{ item.title }}</div>
          <div class="text-caption text-medium-emphasis">{{ item.searches }} meklējumi</div>
        </div>
      </div>
    </v-card>
  </v-col>
</template>

<script setup>
const trendingBooks = [
  {
    title: "Staburadis",
    searches: 12,
    image: "https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=80",
  },
  {
    title: "1984",
    searches: 8,
    image: "https://images.unsplash.com/photo-1543002588-bfa74002ed7e?w=80",
  },
]

const suggestedFriends = [
  { name: "Māris R.", initials: "MR", mutual: 8, color: "pink" },
  { name: "Elīna L.", initials: "EL", mutual: 5, color: "indigo" },
]
</script>

<style scoped>
.panel-card {
  background: rgb(var(--v-theme-surface)) !important;
  color: rgb(var(--v-theme-on-surface)) !important;
  border: 1px solid rgba(var(--v-theme-on-surface), 0.08);
}
</style>
