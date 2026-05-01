<template>
  <v-app>
    <!-- Top Navigation Bar -->
    <v-app-bar color="white" elevation="1" class="px-4">
      <div class="d-flex align-center" style="width: 280px;">
        <router-link to="/main" class="text-decoration-none">
          <h1 class="text-h5 font-weight-bold text-primary">BookSwap</h1>
        </router-link>
      </div>

      <!-- Search Bar -->
      <v-text-field
        v-model="globalSearch"
        placeholder="Meklēt grāmatas, lietotājus..."
        prepend-inner-icon="mdi-magnify"
        variant="outlined"
        density="compact"
        hide-details
        single-line
        class="mx-4"
        style="max-width: 400px;"
        bg-color="grey-lighten-4"
      ></v-text-field>

      <v-spacer></v-spacer>

      <!-- Nav Icons -->
      <v-btn icon variant="text" class="mr-1" href="/main">
        <v-icon>mdi-home</v-icon>
      </v-btn>
      <v-btn icon variant="text" class="mr-1" href="/library">
        <v-icon>mdi-book-open-variant</v-icon>
      </v-btn>
      <v-btn icon variant="text" class="mr-1" href="/exchanges">
        <v-icon>mdi-swap-horizontal</v-icon>
      </v-btn>
      <v-btn icon variant="text" class="mr-1">
        <v-icon>mdi-bell-outline</v-icon>
      </v-btn>
      
      <!-- User Avatar -->
      <v-avatar size="36" color="primary" class="ml-2" style="cursor: pointer;">
        <span class="text-white text-caption">JD</span>
      </v-avatar>
    </v-app-bar>

    <v-main>
      <v-container fluid class="bg-grey-lighten-5 pa-0">
        <v-container class="py-6" style="max-width: 1200px;">
          
          <v-row>
            <!-- Left Sidebar -->
            <v-col cols="12" md="3" class="d-none d-md-block">
              <v-card rounded="xl" elevation="0" class="pa-4 mb-4" color="transparent">
                <div class="d-flex align-center mb-4">
                  <v-avatar size="48" color="primary" class="mr-3">
                    <span class="text-white">JD</span>
                  </v-avatar>
                  <div>
                    <div class="text-subtitle-1 font-weight-bold">Jānis Daugulis</div>
                    <div class="text-caption text-medium-emphasis">12 grāmatas</div>
                  </div>
                </div>
                <v-divider class="mb-3"></v-divider>
                <v-list density="compact" bg-color="transparent">
                  <v-list-item prepend-icon="mdi-home" title="Sākums" value="home" href="/main"></v-list-item>
                  <v-list-item prepend-icon="mdi-book-open-variant" title="Mana bibliotēka" value="library" href="/library"></v-list-item>
                  <v-list-item prepend-icon="mdi-swap-horizontal" title="Manas apmaiņas" value="swaps" href="/exchanges" active-color="primary"></v-list-item>
                  <v-list-item prepend-icon="mdi-message-text" title="Ziņas" value="messages"></v-list-item>
                  <v-list-item prepend-icon="mdi-account-group" title="Draugi" value="friends"></v-list-item>
                </v-list>
              </v-card>

              <!-- Quick Stats -->
              <v-card rounded="xl" elevation="0" class="pa-4" color="transparent">
                <div class="text-subtitle-2 font-weight-bold mb-3">Apmaiņu statistika</div>
                <div class="d-flex justify-space-between mb-2">
                  <span class="text-body-2">Kopā apmainīts</span>
                  <span class="text-body-2 font-weight-bold">8</span>
                </div>
                <div class="d-flex justify-space-between mb-2">
                  <span class="text-body-2">Gaida apmaiņu</span>
                  <span class="text-body-2 font-weight-bold">3</span>
                </div>
                <div class="d-flex justify-space-between">
                  <span class="text-body-2">Jūsu piedāvājumi</span>
                  <span class="text-body-2 font-weight-bold">2</span>
                </div>
              </v-card>
            </v-col>

            <!-- Main Content -->
            <v-col cols="12" md="6">
              <!-- Header -->
              <div class="d-flex justify-space-between align-center mb-6">
                <div>
                  <h1 class="text-h4 font-weight-bold">Manas apmaiņas</h1>
                  <p class="text-body-1 text-medium-emphasis">Sekojiet līdzi savām grāmatu apmaiņām</p>
                </div>
                <v-btn color="primary" prepend-icon="mdi-plus">
                  Jauna apmaiņa
                </v-btn>
              </div>

              <!-- Status Tabs -->
              <v-tabs v-model="activeTab" color="primary" class="mb-4">
                <v-tab value="pending">Gaida<span class="ml-2">(3)</span></v-tab>
                <v-tab value="active"> Aktīvas<span class="ml-2">(2)</span></v-tab>
                <v-tab value="completed"> Pabeigtas<span class="ml-2">(8)</span></v-tab>
              </v-tabs>

              <!-- Pending Exchanges -->
              <v-window v-model="activeTab">
                <v-window-item value="pending">
                  <!-- Exchange Request 1 -->
                  <v-card rounded="xl" elevation="2" class="pa-4 mb-4">
                    <div class="d-flex justify-space-between align-center mb-3">
                      <div class="d-flex align-center">
                        <v-avatar size="40" color="warning" class="mr-3">
                          <v-icon color="white">mdi-clock-outline</v-icon>
                        </v-avatar>
                        <div>
                          <div class="text-subtitle-1 font-weight-bold">Apmaiņas pieprasījums</div>
                          <div class="text-caption text-medium-emphasis">Gaida jūsu apstiprinājumu</div>
                        </div>
                      </div>
                      <v-chip color="warning" variant="tonal">Gaida</v-chip>
                    </div>
                    
                    <v-divider class="mb-3"></v-divider>
                    
                    <div class="d-flex align-center justify-space-between">
                      <div class="d-flex align-center">
                        <v-img
                          src="https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=150"
                          width="60"
                          height="90"
                          class="rounded-lg mr-3"
                          cover
                        ></v-img>
                        <div>
                          <div class="text-body-2 font-weight-bold">Jūs dodat:</div>
                          <div class="text-body-1">Pērkoņa zirgs</div>
                          <div class="text-caption text-medium-emphasis">Ainažu Frics</div>
                        </div>
                      </div>
                      
                      <v-icon size="32" color="primary">mdi-arrow-left-right</v-icon>
                      
                      <div class="d-flex align-center">
                        <div class="text-right mr-3">
                          <div class="text-body-2 font-weight-bold">Jūs saņemat:</div>
                          <div class="text-body-1">Melu anatomija</div>
                          <div class="text-caption text-medium-emphasis">Māra Bērziņa</div>
                        </div>
                        <v-img
                          src="https://images.unsplash.com/photo-1512820790803-83ca734da794?w=150"
                          width="60"
                          height="90"
                          class="rounded-lg"
                          cover
                        ></v-img>
                      </div>
                    </div>
                    
                    <v-divider class="my-3"></v-divider>
                    
                    <div class="d-flex align-center">
                      <v-avatar size="28" color="secondary" class="mr-2">
                        <span class="text-white text-caption">AK</span>
                      </v-avatar>
                      <span class="text-body-2">Anna Kalniņa</span>
                      <v-spacer></v-spacer>
                      <span class="text-caption text-medium-emphasis mr-4">Rīga</span>
                      <v-btn variant="outlined" size="small" class="mr-2">Noraidīt</v-btn>
                      <v-btn color="primary" size="small">Apstiprināt</v-btn>
                    </div>
                  </v-card>

                  <!-- Exchange Request 2 -->
                  <v-card rounded="xl" elevation="2" class="pa-4 mb-4">
                    <div class="d-flex justify-space-between align-center mb-3">
                      <div class="d-flex align-center">
                        <v-avatar size="40" color="info" class="mr-3">
                          <v-icon color="white">mdi-swap-horizontal</v-icon>
                        </v-avatar>
                        <div>
                          <div class="text-subtitle-1 font-weight-bold">Jūsu piedāvājums</div>
                          <div class="text-caption text-medium-emphasis">Gaida atbildi</div>
                        </div>
                      </div>
                      <v-chip color="info" variant="tonal">Gaida atbildi</v-chip>
                    </div>
                    
                    <v-divider class="mb-3"></v-divider>
                    
                    <div class="d-flex align-center justify-space-between">
                      <div class="d-flex align-center">
                        <v-img
                          src="https://images.unsplash.com/photo-1543002588-bfa74002ed7e?w=150"
                          width="60"
                          height="90"
                          class="rounded-lg mr-3"
                          cover
                        ></v-img>
                        <div>
                          <div class="text-body-2 font-weight-bold">Jūs piedāvājat:</div>
                          <div class="text-body-1">1984</div>
                          <div class="text-caption text-medium-emphasis">Džordžs Orvels</div>
                        </div>
                      </div>
                      
                      <v-icon size="32" color="primary">mdi-arrow-left-right</v-icon>
                      
                      <div class="d-flex align-center">
                        <div class="text-right mr-3">
                          <div class="text-body-2 font-weight-bold">Pret:</div>
                          <div class="text-body-1">Prāts bez robežām</div>
                          <div class="text-caption text-medium-emphasis">Deivids Dočs</div>
                        </div>
                        <v-img
                          src="https://images.unsplash.com/photo-1532012197267-da84d127e765?w=150"
                          width="60"
                          height="90"
                          class="rounded-lg"
                          cover
                        ></v-img>
                      </div>
                    </div>
                    
                    <v-divider class="my-3"></v-divider>
                    
                    <div class="d-flex align-center">
                      <v-avatar size="28" color="teal" class="mr-2">
                        <span class="text-white text-caption">PM</span>
                      </v-avatar>
                      <span class="text-body-2">Pēteris Mārtins</span>
                      <v-spacer></v-spacer>
                      <span class="text-caption text-medium-emphasis mr-4">Liepāja</span>
                      <v-btn variant="text" size="small" color="error">Atcelt</v-btn>
                    </div>
                  </v-card>

                  <!-- Exchange Request 3 -->
                  <v-card rounded="xl" elevation="2" class="pa-4">
                    <div class="d-flex justify-space-between align-center mb-3">
                      <div class="d-flex align-center">
                        <v-avatar size="40" color="info" class="mr-3">
                          <v-icon color="white">mdi-swap-horizontal</v-icon>
                        </v-avatar>
                        <div>
                          <div class="text-subtitle-1 font-weight-bold">Jūsu piedāvājums</div>
                          <div class="text-caption text-medium-emphasis">Gaida atbildi</div>
                        </div>
                      </div>
                      <v-chip color="info" variant="tonal">Gaida atbildi</v-chip>
                    </div>
                    
                    <v-divider class="mb-3"></v-divider>
                    
                    <div class="d-flex align-center justify-space-between">
                      <div class="d-flex align-center">
                        <v-img
                          src="https://images.unsplash.com/photo-1589829085413-56de8ae18c73?w=150"
                          width="60"
                          height="90"
                          class="rounded-lg mr-3"
                          cover
                        ></v-img>
                        <div>
                          <div class="text-body-2 font-weight-bold">Jūs piedāvājat:</div>
                          <div class="text-body-1">Čempionu brokastis</div>
                          <div class="text-caption text-medium-emphasis">Džefs Kinels</div>
                        </div>
                      </div>
                      
                      <v-icon size="32" color="primary">mdi-arrow-left-right</v-icon>
                      
                      <div class="d-flex align-center">
                        <div class="text-right mr-3">
                          <div class="text-body-2 font-weight-bold">Pret:</div>
                          <div class="text-body-1">Zaļā mile</div>
                          <div class="text-caption text-medium-emphasis">Ainažu Frics</div>
                        </div>
                        <v-img
                          src="https://images.unsplash.com/photo-1516979187457-637abb4f9353?w=150"
                          width="60"
                          height="90"
                          class="rounded-lg"
                          cover
                        ></v-img>
                      </div>
                    </div>
                    
                    <v-divider class="my-3"></v-divider>
                    
                    <div class="d-flex align-center">
                      <v-avatar size="28" color="orange" class="mr-2">
                        <span class="text-white text-caption">LV</span>
                      </v-avatar>
                      <span class="text-body-2">Līga Vītola</span>
                      <v-spacer></v-spacer>
                      <span class="text-caption text-medium-emphasis mr-4">Jelgava</span>
                      <v-btn variant="text" size="small" color="error">Atcelt</v-btn>
                    </div>
                  </v-card>
                </v-window-item>

                <!-- Active Exchanges -->
                <v-window-item value="active">
                  <v-card rounded="xl" elevation="2" class="pa-4 mb-4">
                    <div class="d-flex justify-space-between align-center mb-3">
                      <div class="d-flex align-center">
                        <v-avatar size="40" color="success" class="mr-3">
                          <v-icon color="white">mdi-truck-delivery</v-icon>
                        </v-avatar>
                        <div>
                          <div class="text-subtitle-1 font-weight-bold">Apmaiņa notiek</div>
                          <div class="text-caption text-medium-emphasis">Grāmatas ceļā</div>
                        </div>
                      </div>
                      <v-chip color="success" variant="tonal"> Aktīva</v-chip>
                    </div>
                    
                    <v-divider class="mb-3"></v-divider>
                    
                    <div class="d-flex align-center justify-space-between">
                      <div class="d-flex align-center">
                        <v-img
                          src="https://images.unsplash.com/photo-1541961017774-22349e4a1262?w=150"
                          width="60"
                          height="90"
                          class="rounded-lg mr-3"
                          cover
                        ></v-img>
                        <div>
                          <div class="text-body-2 font-weight-bold">Jūs saņemat:</div>
                          <div class="text-body-1">Staburadis</div>
                          <div class="text-caption text-medium-emphasis">Vilis Kadēns</div>
                        </div>
                      </div>
                      
                      <v-icon size="32" color="success">mdi-arrow-left-right</v-icon>
                      
                      <div class="d-flex align-center">
                        <div class="text-right mr-3">
                          <div class="text-body-2 font-weight-bold">Jūs devāt:</div>
                          <div class="text-body-1">Mērnieks</div>
                          <div class="text-caption text-medium-emphasis">Kārlis Skalbe</div>
                        </div>
                        <v-img
                          src="https://images.unsplash.com/photo-1476275466078-4007374efbbe?w=150"
                          width="60"
                          height="90"
                          class="rounded-lg"
                          cover
                        ></v-img>
                      </div>
                    </div>
                    
                    <v-divider class="my-3"></v-divider>
                    
                    <div class="d-flex align-center justify-space-between">
                      <div class="d-flex align-center">
                        <v-avatar size="28" color="pink" class="mr-2">
                          <span class="text-white text-caption">MR</span>
                        </v-avatar>
                        <span class="text-body-2">Māris R.</span>
                      </div>
                      <div>
                        <v-btn size="small" variant="text" prepend-icon="mdi-message">Ziņa</v-btn>
                        <v-btn size="small" color="primary" variant="tonal">Apstiprināt saņemšanu</v-btn>
                      </div>
                    </div>
                  </v-card>

                  <v-card rounded="xl" elevation="2" class="pa-4">
                    <div class="d-flex justify-space-between align-center mb-3">
                      <div class="d-flex align-center">
                        <v-avatar size="40" color="primary" class="mr-3">
                          <v-icon color="white">mdi-handshake</v-icon>
                        </v-avatar>
                        <div>
                          <div class="text-subtitle-1 font-weight-bold">Tikšanās plānota</div>
                          <div class="text-caption text-medium-emphasis">Satiksieties klātienē</div>
                        </div>
                      </div>
                      <v-chip color="primary" variant="tonal">Tikšanās</v-chip>
                    </div>
                    
                    <v-divider class="mb-3"></v-divider>
                    
                    <div class="d-flex align-center justify-space-between">
                      <div class="d-flex align-center">
                        <v-img
                          src="https://images.unsplash.com/photo-1512820790803-83ca734da794?w=150"
                          width="60"
                          height="90"
                          class="rounded-lg mr-3"
                          cover
                        ></v-img>
                        <div>
                          <div class="text-body-2 font-weight-bold">Jūs dodat:</div>
                          <div class="text-body-1">Melu anatomija</div>
                          <div class="text-caption text-medium-emphasis">Māra Bērziņa</div>
                        </div>
                      </div>
                      
                      <v-icon size="32" color="primary">mdi-arrow-left-right</v-icon>
                      
                      <div class="d-flex align-center">
                        <div class="text-right mr-3">
                          <div class="text-body-2 font-weight-bold">Pret:</div>
                          <div class="text-body-1">Prāts bez robežām</div>
                          <div class="text-caption text-medium-emphasis">Deivids Dočs</div>
                        </div>
                        <v-img
                          src="https://images.unsplash.com/photo-1532012197267-da84d127e765?w=150"
                          width="60"
                          height="90"
                          class="rounded-lg"
                          cover
                        ></v-img>
                      </div>
                    </div>
                    
                    <v-divider class="my-3"></v-divider>
                    
                    <div class="d-flex align-center justify-space-between">
                      <div class="d-flex align-center">
                        <v-avatar size="28" color="indigo" class="mr-2">
                          <span class="text-white text-caption">EL</span>
                        </v-avatar>
                        <div>
                          <span class="text-body-2">Elīna L.</span>
                          <div class="text-caption text-primary">📍 Rīga, Centrs — 15:00</div>
                        </div>
                      </div>
                      <div>
                        <v-btn size="small" variant="text" prepend-icon="mdi-calendar">Mainīt</v-btn>
                        <v-btn size="small" color="primary">Apstiprināts</v-btn>
                      </div>
                    </div>
                  </v-card>
                </v-window-item>

                <!-- Completed Exchanges -->
                <v-window-item value="completed">
                  <v-card rounded="xl" elevation="0" class="pa-4 mb-4" color="grey-lighten-4">
                    <div class="d-flex justify-space-between align-center">
                      <div class="d-flex align-center">
                        <v-img
                          src="https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=150"
                          width="50"
                          height="75"
                          class="rounded-lg mr-3"
                          cover
                        ></v-img>
                        <div>
                          <div class="text-body-1 font-weight-bold">Pērkoņa zirgs</div>
                          <div class="text-caption text-medium-emphasis">pret "Zaļā mile"</div>
                        </div>
                      </div>
                      <div class="text-right">
                        <v-chip color="success" size="small" class="mb-1">Pabeigta</v-chip>
                        <div class="text-caption text-medium-emphasis">2026. gada 15. apr.</div>
                      </div>
                    </div>
                  </v-card>

                  <v-card rounded="xl" elevation="0" class="pa-4 mb-4" color="grey-lighten-4">
                    <div class="d-flex justify-space-between align-center">
                      <div class="d-flex align-center">
                        <v-img
                          src="https://images.unsplash.com/photo-1543002588-bfa74002ed7e?w=150"
                          width="50"
                          height="75"
                          class="rounded-lg mr-3"
                          cover
                        ></v-img>
                        <div>
                          <div class="text-body-1 font-weight-bold">1984</div>
                          <div class="text-caption text-medium-emphasis">pret "Lauva"</div>
                        </div>
                      </div>
                      <div class="text-right">
                        <v-chip color="success" size="small" class="mb-1">Pabeigta</v-chip>
                        <div class="text-caption text-medium-emphasis">2026. gada 2. apr.</div>
                      </div>
                    </div>
                  </v-card>

                  <v-card rounded="xl" elevation="0" class="pa-4 mb-4" color="grey-lighten-4">
                    <div class="d-flex justify-space-between align-center">
                      <div class="d-flex align-center">
                        <v-img
                          src="https://images.unsplash.com/photo-1476275466078-4007374efbbe?w=150"
                          width="50"
                          height="75"
                          class="rounded-lg mr-3"
                          cover
                        ></v-img>
                        <div>
                          <div class="text-body-1 font-weight-bold">Mērnieks</div>
                          <div class="text-caption text-medium-emphasis">pret "Pīrāgs"</div>
                        </div>
                      </div>
                      <div class="text-right">
                        <v-chip color="success" size="small" class="mb-1">Pabeigta</v-chip>
                        <div class="text-caption text-medium-emphasis">2026. gada 28. marts</div>
                      </div>
                    </div>
                  </v-card>

                  <v-card rounded="xl" elevation="0" class="pa-4 mb-4" color="grey-lighten-4">
                    <div class="d-flex justify-space-between align-center">
                      <div class="d-flex align-center">
                        <v-img
                          src="https://images.unsplash.com/photo-1516979187457-637abb4f9353?w=150"
                          width="50"
                          height="75"
                          class="rounded-lg mr-3"
                          cover
                        ></v-img>
                        <div>
                          <div class="text-body-1 font-weight-bold">Zaļā mile</div>
                          <div class="text-caption text-medium-emphasis">pret "Pērkoņa zirgs"</div>
                        </div>
                      </div>
                      <div class="text-right">
                        <v-chip color="success" size="small" class="mb-1">Pabeigta</v-chip>
                        <div class="text-caption text-medium-emphasis">2026. gada 15. marts</div>
                      </div>
                    </div>
                  </v-card>

                  <v-card rounded="xl" elevation="0" class="pa-4 mb-4" color="grey-lighten-4">
                    <div class="d-flex justify-space-between align-center">
                      <div class="d-flex align-center">
                        <v-img
                          src="https://images.unsplash.com/photo-1589829085413-56de8ae18c73?w=150"
                          width="50"
                          height="75"
                          class="rounded-lg mr-3"
                          cover
                        ></v-img>
                        <div>
                          <div class="text-body-1 font-weight-bold">Čempionu brokastis</div>
                          <div class="text-caption text-medium-emphasis">pret "Veiksmes 7"</div>
                        </div>
                      </div>
                      <div class="text-right">
                        <v-chip color="success" size="small" class="mb-1">Pabeigta</v-chip>
                        <div class="text-caption text-medium-emphasis">2026. gada 1. marts</div>
                      </div>
                    </div>
                  </v-card>

                  <!-- Load More -->
                  <div class="text-center mt-4">
                    <v-btn variant="text" color="primary">Ielādēt vēl</v-btn>
                  </div>
                </v-window-item>
              </v-window>
            </v-col>

            <!-- Right Sidebar -->
            <v-col cols="12" md="3" class="d-none d-md-block">
              <!-- Quick Actions -->
              <v-card rounded="xl" elevation="0" class="pa-4 mb-4" color="transparent">
                <div class="text-subtitle-1 font-weight-bold mb-3">Ātrās darbības</div>
                <v-btn block variant="tonal" color="primary" class="mb-2" prepend-icon="mdi-plus">Jauna apmaiņa</v-btn>
                <v-btn block variant="outlined" prepend-icon="mdi-magnify">Pārlūkot piedāvājumus</v-btn>
              </v-card>

              <!-- Recent Messages -->
              <v-card rounded="xl" elevation="0" class="pa-4" color="transparent">
                <div class="text-subtitle-1 font-weight-bold mb-3">Nesenās ziņas</div>
                
                <div class="d-flex align-center mb-3">
                  <v-avatar size="36" color="secondary" class="mr-3">
                    <span class="text-white">AK</span>
                  </v-avatar>
                  <div class="flex-grow-1">
                    <div class="text-body-2 font-weight-bold">Anna Kalniņa</div>
                    <div class="text-caption text-medium-emphasis">Super, tiksimies!</div>
                  </div>
                  <div class="text-caption text-grey">2h</div>
                </div>

                <div class="d-flex align-center mb-3">
                  <v-avatar size="36" color="teal" class="mr-3">
                    <span class="text-white">PM</span>
                  </v-avatar>
                  <div class="flex-grow-1">
                    <div class="text-body-2 font-weight-bold">Pēteris Mārtins</div>
                    <div class="text-caption text-medium-emphasis">Vēl varu piedāvāt...</div>
                  </div>
                  <div class="text-caption text-grey">5h</div>
                </div>

                <div class="d-flex align-center">
                  <v-avatar size="36" color="orange" class="mr-3">
                    <span class="text-white">LV</span>
                  </v-avatar>
                  <div class="flex-grow-1">
                    <div class="text-body-2 font-weight-bold">Līga Vītola</div>
                    <div class="text-caption text-medium-emphasis">Paldies par grāmatu!</div>
                  </div>
                  <div class="text-caption text-grey">1d</div>
                </div>
              </v-card>
            </v-col>
          </v-row>

        </v-container>
      </v-container>
    </v-main>
  </v-app>
</template>

<script setup>
import { ref } from 'vue'

const globalSearch = ref('')
const activeTab = ref('pending')
</script>