<template>
  <v-container class="py-10">
    <!-- Hero -->
    <v-row class="mb-10">
      <v-col cols="12" md="7">
        <h1 class="text-h3 font-weight-bold mb-4">
          Exchange books. Connect with people. 📚
        </h1>

        <p class="text-body-1 mb-6">
          BookSwap is a social platform where users can list books, communicate with others,
          and exchange books easily.
        </p>

        <v-btn color="primary" class="mr-3">Explore books</v-btn>
        <v-btn variant="outlined">Join now</v-btn>
      </v-col>
    </v-row>

    <!-- Newest books -->
    <h2 class="text-h5 font-weight-bold mb-4">Newest books</h2>

    <v-row>
      <v-col cols="12" md="4" v-for="n in 3" :key="n">
        <v-card>
          <v-card-title>Book title {{ n }}</v-card-title>
          <v-card-subtitle>Author name</v-card-subtitle>
          <v-card-text>
            Condition: Good
          </v-card-text>
          <v-card-actions>
            <v-btn color="primary" variant="flat">Request exchange</v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>

    <!-- Social preview -->
    <v-divider class="my-10" />

    <v-row>
      <v-col cols="12" md="8">
        <h2 class="text-h5 font-weight-bold mb-3">More than just books</h2>
        <p class="text-body-1">
          Chat with other users, discover local readers, and build your personal library
          through exchanges.
        </p>
      </v-col>
    </v-row>
  </v-container>
</template>
