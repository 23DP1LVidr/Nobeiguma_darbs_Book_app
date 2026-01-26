<template>
  <v-container class="py-10">
    <!-- Hero -->
    <v-row class="mb-10">
      <v-col cols="12" md="7">
        <h1 class="text-h3 font-weight-bold mb-4">
          Maini grāmatas. Sazinies ar cilvēkiem. 📚
        </h1>

        <p class="text-body-1 mb-6">
          BookSwap ir sociāla platforma, kur lietotāji var pievienot grāmatas, sazināties ar citiem
          un ērti apmainīties ar grāmatām.
        </p>

        <v-btn color="primary" class="mr-3">Apskatīt grāmatas</v-btn>
        <v-btn variant="outlined">Pievienoties tagad</v-btn>
      </v-col>
    </v-row>

    <!-- Newest books -->
    <h2 class="text-h5 font-weight-bold mb-4">Jaunākās grāmatas</h2>

    <v-row>
      <v-col cols="12" md="4" v-for="n in 3" :key="n">
        <v-card>
          <v-card-title>Grāmatas nosaukums {{ n }}</v-card-title>
          <v-card-subtitle>Autora vārds</v-card-subtitle>
          <v-card-text>
            Stāvoklis: Labs
          </v-card-text>
          <v-card-actions>
            <v-btn color="primary" variant="flat">Pieprasīt apmaiņu</v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>

    <!-- Social preview -->
    <v-divider class="my-10" />

    <v-row>
      <v-col cols="12" md="8">
        <h2 class="text-h5 font-weight-bold mb-3">Vairāk nekā tikai grāmatas</h2>
        <p class="text-body-1">
          Sazinies ar citiem lietotājiem, atklāj vietējos lasītājus un veido savu personīgo bibliotēku,
          apmainoties ar grāmatām.
        </p>
      </v-col>
    </v-row>
  </v-container>
</template>
