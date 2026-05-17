<template>
  <div class="mb-4">
 
    <v-select
      v-model="selectedFolder"
      :items="formattedFolders"
      label="Vælg mappe"
      outlined
      @change="emitFolder"
    ></v-select>
    <v-btn color="primary" small class="mt-2" @click="createFolder">
      Opret ny mappe
    </v-btn>


    <!-- Liste med mapper til drop -->
    <v-list two-line class="mt-2" v-if="0">
      <v-list-item
        v-for="folder in folders"
        :key="folder.id"
        @dragover.prevent
        @drop.prevent="dropImage(folder, $event)"
      > 

        <v-list-item-content >
          <v-list-item-title>
            <span :style="{ paddingLeft: (folder.depth * 20) + 'px' }">
              {{ folder.name }}
            </span>
          </v-list-item-title>
        </v-list-item-content>
      </v-list-item>
    </v-list>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  props: {
    folders: { type: Array, default: () => [] }
  },
  data() {
    return {
      selectedFolder: null
    }
  },
  computed: {
    formattedFolders() {
      // Konverter mapper til objekter med text/value, evt. med indentation baseret på "depth"
      return this.folders.map(folder => {
        let prefix = ''
        if (folder.depth) {
          prefix = '— '.repeat(folder.depth)
        }
        return { text: prefix + folder.name, value: folder }
      })
    }
  },

  watch: {
    folders: {
      handler(newFolders) {
        if (newFolders.length > 0 && !this.selectedFolder) {
          this.selectedFolder = newFolders[0] // Sætter første mappe som default
          this.emitFolder() // Udsender event, så parent-komponenten ved det
        }
      },
      immediate: true, // Kør watcheren med det samme, når komponenten loades
      deep: true
    }
  },


  methods: {
    emitFolder() {
      this.$emit('folderSelected', this.selectedFolder)
    },
    createFolder() {
      const name = prompt('Indtast navn på ny mappe:')
      if (name) {
        axios
          .post(route("api.folders.index",{
            name: name,
            parent_id: this.selectedFolder ? this.selectedFolder.id : null
          }))
          .then(response => {
            this.$emit('folderCreated', response.data)
          })
          .catch(error => console.error('Oprettelse af mappe fejlede:', error))
      }
    },
    dropImage(folder, event) {
      const imageId = event.dataTransfer.getData('image_id')
      if (imageId) {
        this.$emit('moveImage', { imageId, folder })
      }
    }
  }

}
</script>

<style scoped>
/* Tilpas evt. styling af folder-listen */
</style>
