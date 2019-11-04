<template>
  <div id="app">
    <Index v-if="isRouterAlive"></Index>
  </div>
</template>

<script>
  import Index from './views/Index';

  export default {
      data(){
          return  {
              isRouterAlive: true,
          }
      },
      components:{
          Index
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
          }
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
