<template>
  <a-layout id="components-layout-demo-custom-trigger">
    <a-layout-sider :trigger="null" collapsible v-model="collapsed">
      <div class="logo">
        <div v-if="token === null || token == 'null' || token == undefined">
          <span>登录</span>
          <a-icon type="login" title="登录" @click="goToLogin"></a-icon>
        </div>
        <div v-else>
          <span>{{name}}</span>
          <a-icon type="logout" title="退出" style="color:red" @click="out"></a-icon>
        </div>
      </div>
      <a-menu theme="dark" mode="inline" :defaultSelectedKeys="['1']" :style="{textAlign:'left'}">
        <a-sub-menu key="sub1">
          <span slot="title"><a-icon type="bars"/>导航菜单</span>
          <a-menu-item key="subItem1">
            <router-link to="/">
              查看导航
            </router-link>
          </a-menu-item>
          <a-menu-item key="subItem2">
            <router-link to="/NavAdd">
              添加导航
            </router-link>
          </a-menu-item>
        </a-sub-menu>
        <a-sub-menu key="sub2">
          <span slot="title"><a-icon type="bars"/>分类菜单</span>
          <a-menu-item key="subItem3">
            <router-link to="/Cate">
              查看分类
            </router-link>
          </a-menu-item>
          <a-menu-item key="subItem4">
            <router-link to="/Cate/Create">
              添加分类
            </router-link>
          </a-menu-item>
        </a-sub-menu>
        <a-sub-menu key="sub3">
          <span slot="title"><a-icon type="bars"/>商品菜单</span>
          <a-menu-item key="subItem5">
            <router-link to="/Product">
              查看商品
            </router-link>
          </a-menu-item>
          <a-menu-item key="subItem6">
            <router-link to="/Product/Create">
              添加商品
            </router-link>
          </a-menu-item>
        </a-sub-menu>
      </a-menu>
    </a-layout-sider>
    <a-layout>
      <a-layout-header style="background: #fff; padding: 0;text-align: left">
        <a-icon
          class="trigger"
          :type="collapsed ? 'menu-unfold' : 'menu-fold'"
          @click="()=> collapsed = !collapsed"
        />
      </a-layout-header>
      <a-layout-content
        :style="{ margin: '24px 16px', padding: '24px', background: '#fff', minHeight: '500px' }"
      >
        <router-view></router-view>
      </a-layout-content>
    </a-layout>
  </a-layout>
</template>

<script>
  import Api from "../Api";
  import qs from "qs";
    export default {
        data() {
            return {
                collapsed: false,
                token: null,
                name:'',
            };
        },
        components: {},
        methods:{
            out(){
                let token = localStorage.getItem('token');

                this.axios.post(Api.Out,qs.stringify({token:token})).then((res)=>{
                    if (res.data.status) {
                        localStorage.setItem('token',null);
                        this.$router.push('/Login');
                    }
                });
            },
            goToLogin(){
                this.$router.push('/Login');
            }
        },
        created() {
            this.name = localStorage.getItem('name');
            this.token = localStorage.getItem("token");
        }
    }
</script>

<style lang="scss">
  #components-layout-demo-custom-trigger {
    .trigger {
      font-size: 18px;
      line-height: 64px;
      padding: 0 24px;
      cursor: pointer;
      transition: color 0.3s;
    }

    .trigger:hover {
      color: #1890ff;
    }

    .logo {
      height: 32px;
      background: rgba(255, 255, 255, 0.2);
      margin: 16px;
      position: relative;
      color: #ffffff;

      span{
        font-size: 12px;
        position: absolute;
        top: 25%;
        left: 10px;
      }

      i{
        position: absolute;
        top: 25%;
        right: 10px;
        cursor: pointer;
      }
    }
  }
</style>
