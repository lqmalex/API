<template>
  <div id="app">
    <Index v-if="isRouterAlive && this.$route.path != '/Login' && this.$route.path != '/Register'"></Index>
    <User v-else></User>
  </div>
</template>

<script>
  import Index from './views/Index';
  import User from "./views/User";
  export default {
      data(){
          return  {
              isRouterAlive: true,
          }
      },
      components:{
          Index,
          User
      },
      methods:{
          /**
           * 刷新页面
           */
          reload() {
              this.isRouterAlive = false;
              this.$nextTick(function() {
                  this.isRouterAlive = true;
              });
          },
          getToken() {
              let token = localStorage.getItem('token');

              if (token === null || token == 'null') {
                  this.$router.push('/Login');
              }
          }
      },
      created(){
        this.getToken();
      },
      /**
       * 将reload抛出
       */
      provide() {
          return {
              reload: this.reload,
              Date1: this.Date1,
              Date2: this.Date2
          };
      },
  }
</script>

<style lang="scss">
#app {
  font-family: 'Avenir', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
}

#nav {
  padding: 30px;

  a {
    font-weight: bold;
    color: #2c3e50;

    &.router-link-exact-active {
      color: #42b983;
    }
  }
}
</style>
