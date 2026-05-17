
import Vue from "vue"
import MediaPage from "./Pages/MediaPage.vue"


//import TestImageSelect from "./Components/TestImageSelect.vue"

Vue.component('ImageSelect', require('./Components/ImageSelect.vue').default);

window.router.addRoute({ path: '/media', component: MediaPage, name: "module.media.index"})


//window.app.component('TestImageSelect',TestImageSelect);

