<template>
  <section>
    <div class="info-container">
      <div
        v-for="file in files"
        :key="file.id"
        class="info-row"
        @click="openPreview(file)"
      >
        <!-- Ikon (billede eller PDF ikon) -->
        <div class="info-thumbnail">
          <img
            v-if="file.type === 'images'"
            :src="file.src"
            :alt="file.file_name"
          />
          <img
            v-else
            :src="getImageLink(file)"
            :alt="file.type"
          />
        </div>

        <!-- Ekstra info om filen -->
        <div class="info-details">
          <div class="name"><strong>{{ file.name }}</strong></div>
          <div class="file-name">{{ file.file_name }}</div>
          <div class="file-size">{{ file.size }}</div>
          <div class="file-action">
            <v-chip small>Åbn fil</v-chip>
          </div>
        </div>
      </div>
    </div>

    <!-- Dialog med iframe -->
    <v-dialog 
      v-model="dialog" 
      max-width="90%" 
      persistent
      hide-overlay
      hide-footer
      fullscreen
    >
      <v-card>
        <v-toolbar flat>
          <v-toolbar-title>Forhåndsvisning <span v-if="currentFile"> - {{currentFile.name}} - {{currentFile.file_name}}</span></v-toolbar-title>
          <v-spacer></v-spacer>
          <v-btn icon @click="dialog = false">
            <v-icon>{{close}}</v-icon>
          </v-btn>
        </v-toolbar>
        <v-card-text style="height: 80vh; padding: 0;">

          <Loading v-if="loading"/>

          <iframe
            v-if="currentFile"
            :src="currentFile.src"
            @load="iframeLoaded"
          ></iframe>

        </v-card-text>
      </v-card>
    </v-dialog>
  </section>
</template>

<script>

import { mdiCloseCircleOutline } from '@mdi/js';

export default {
  props: {
    files: {
      type: Array,
      required: true
    }
  },
  data() {
    return {
      dialog: false,
      currentFile: null,
      close: mdiCloseCircleOutline,
      loading: true
    };
  },
  methods: {
    openPreview(file) {

      let self = this

      this.loading = true

      this.currentFile = ""

      this.$nextTick(() => {
        
        self.currentFile = file;

      })
      
      this.dialog = true;
    },

    iframeLoaded(){

      this.loading = false

    },
    getImageLink(file) {
      const fileType = file.file_name.split('.').pop().toLowerCase();
      const iconPath = this.getStandarsIcon(`${fileType}-icon.png`);
      return iconPath;
    },
    getStandarsIcon(icon) {
      return "/Modules/Media/images/" + icon;
    }
  }
};
</script>

<style scoped>
.info-container {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.v-dialog{
  overflow-y: hidden !important;
}

.info-row {
  display: flex;
  align-items: center;
  padding: 12px 20px;
  border: 1px solid #ddd;
  border-radius: 5px;
  cursor: pointer;
  transition: background 0.3s, transform 0.2s;
}

.info-row:hover {
  background-color: #f5f5f5;
}

iframe{
  width: 100%;
  border: none;
  height: calc(100vh - 70px);
}

.info-thumbnail {
  flex: 0 0 60px;
  height: 60px;
  margin-right: 15px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.info-thumbnail img {
  max-width: 100%;
  max-height: 100%;
  object-fit: cover;
  border-radius: 5px;
}

.info-details {
  display: flex;
  flex-direction: column;
}

.file-name {
  color: #555;
}

.file-action {
  padding-top: 5px;
}

.file-action,
.file-url {
  font-size: 14px;
  color: #555;
  word-break: break-all;
}


</style>
