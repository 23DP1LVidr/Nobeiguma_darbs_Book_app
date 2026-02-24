<template>
  <v-container fluid class="hero-bg">
    <v-container class="py-16">
      
      <v-row justify="center" align="center">
        <v-col cols="12" md="8" class="text-center">
          
          <h1 class="hero-title mb-6">
            BookSwap
          </h1>

          <p class="hero-subtitle mb-8">
            Moderna sociālā platforma grāmatu lasītājiem Latvijā.  
            Maini grāmatas, sazinies ar citiem lasītājiem un veido savu personīgo bibliotēku —
            viss vienā skaisti veidotā vidē.
          </p>

          <v-btn color="primary" size="large" class="mr-4">
            Sākt lietot
          </v-btn>

          <v-btn variant="outlined" size="large" href="about">
            Uzzināt vairāk
          </v-btn>

        </v-col>
      </v-row>

      <v-row justify="center" class="mt-16">
        <v-col cols="12" md="10">
          <v-img
            src="https://images.unsplash.com/photo-1512820790803-83ca734da794"
            aspect-ratio="2.2"
            class="hero-image"
            cover
          ></v-img>
        </v-col>
      </v-row>

      <v-row justify="center" class="mt-16">
        <v-col cols="12" md="8" class="text-center">
          <h2 class="section-title mb-4">
            Labāks veids, kā dalīties ar grāmatām
          </h2>
          <p class="section-text">
            BookSwap apvieno grāmatmīļus visā Latvijā modernā un intuitīvā platformā,
            kas iedvesmota no sociālo tīklu pieredzes.  
            Pievieno savas grāmatas, atklāj jaunus nosaukumus un pievienojies
            augošai lasītāju kopienai.
          </p>
        </v-col>
      </v-row>

    </v-container>
  </v-container>
</template>

<script setup>
</script>

<style scoped>
.hero-bg {
  background: #f5f5f7;
  min-height: 100vh;
}

.hero-title {
  font-size: 64px;
  font-weight: 700;
  letter-spacing: -1px;
}

.hero-subtitle {
  font-size: 20px;
  color: #6e6e73;
  max-width: 700px;
  margin: 0 auto;
}

.section-title {
  font-size: 32px;
  font-weight: 600;
}

.section-text {
  font-size: 18px;
  color: #6e6e73;
  max-width: 650px;
  margin: 0 auto;
}

.hero-image {
  border-radius: 24px;
  box-shadow: 0 20px 40px rgba(0,0,0,0.08);
}
</style>
