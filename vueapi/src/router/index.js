import Vue from 'vue'
import VueRouter from 'vue-router'
import Home from '../views/Home.vue'
import Nav from "../components/Nav/Nav";
import NavCreate from "../components/Nav/NavCreate";
import NavEdit from "../components/Nav/NavEdit";
import Cate from "../components/Cate/Cate";
import CateCreate from "../components/Cate/CateCreate";
import Product  from '../components/Pro/Product'
import ProductCreate from "../components/Pro/ProductCreate";
import User from "../views/User";
import Login from "../components/User/Login";

Vue.use(VueRouter)

const routes = [
  {
    path: '/',
    name: 'nav',
    component: Nav
  },
  {
    path: '/about',
    name: 'about',
    // route level code-splitting
    // this generates a separate chunk (about.[hash].js) for this route
    // which is lazy-loaded when the route is visited.
    component: () => import(/* webpackChunkName: "about" */ '../views/About.vue')
  },
  {
    path:'/NavAdd',
    name:'NavAdd',
    component:NavCreate
  },
  {
    path:'/NavEdit',
    name:'/NavEdit',
    component:NavEdit
  },
  {
    path:'/Cate',
    name:'/Cate',
    component:Cate
  },
  {
    path:'/Cate/Create',
    name:'/Cate/Create',
    component:CateCreate
  },
  {
    path:'/Product',
    name:'/Product',
    component:Product
  },
  {
    path:'/Product/Create',
    name:'/Product/Create',
    component:ProductCreate
  },
  {
    path:'/Login',
    name:'/Login',
    component:Login
  },
]

const router = new VueRouter({
  routes
})

export default router
