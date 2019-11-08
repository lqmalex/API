import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'
import Antd from 'ant-design-vue';
import 'ant-design-vue/dist/antd.css';
import axios from 'axios'
import  VueQuillEditor from 'vue-quill-editor'
// require styles 引入样式
import 'quill/dist/quill.core.css'
import 'quill/dist/quill.snow.css'
import 'quill/dist/quill.bubble.css'

Vue.use(VueQuillEditor)

Vue.prototype.axios = axios
Vue.config.productionTip = false
Vue.use(Antd);

// 添加请求拦截器
axios.interceptors.request.use(function (config) {
  // console.log(config);
  // 在发送请求之前做些什么
  if(localStorage.getItem('token')){
    config.headers.Authorization = `Bearer ${localStorage.getItem('token')}`;
  }else{
    router.replace({
      path: '/login',
      query: { redirect: router.currentRoute.fullPath } // 将跳转的路由path作为参数，登录成功后跳转到该路由
    })
  }
  return config;
}, function (error) {
  // 对请求错误做些什么
  return Promise.reject(error);
});

// 添加响应拦截器
axios.interceptors.response.use(
  response => {
    if (response.data.token_status != undefined && response.data.token_status == false) {
      router.replace({
        path: '/Login',
        // query: { redirect: router.currentRoute.fullPath } // 将跳转的路由path作为参数，登录成功后跳转到该路由
      })
    }
    return response
  },
  error => {
    if (error.response) {
      switch (error.response.status) {
        case 401:
          if(localStorage.getItem('token')){
            localStorage.removeItem('token')
          }
          router.replace({
            path: '/login',
            query: { redirect: router.currentRoute.fullPath } // 将跳转的路由path作为参数，登录成功后跳转到该路由
          })
      }
    }
    return Promise.reject(error.response)
  }
);

new Vue({
  router,
  store,
  render: h => h(App)
}).$mount('#app')
