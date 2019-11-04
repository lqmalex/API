const domain = "http://localhost:8888/";
const ip = domain + "api";

//获取 导航
const getNav = ip + '/nav/getNav';
//删除 导航
const DelNav = ip + '/nav/del';
//上传 文件
const NavUpload = ip + '/nav/upload';
//导航 编辑
const NavEdit = ip + '/nav/edit?id=';
//商品数据
const getProduct = ip + '/product/getProduct';
//分类数据
const getCate = ip + '/cate/getCate';
//添加导航
const NavCreate = ip + '/nav/create';
//删除分类
const DelCate = ip + '/cate/delete';
//分类编辑
const CateEdit = ip + '/cate/edit?id=';
//分类添加
const CateCreate = ip + '/cate/create';
//删除商品
const DelProduct = ip + '/product/delete';
//编辑商品 标签 库存
const ProductEdit = ip + '/product/edit';
//商品 上/下架
const ChangeShelf = ip + '/product/changeShelf';
//商品 添加
const ProductCreate = ip + '/product/create';

//全部抛出
export default {
  getNav: getNav,
  DelNav: DelNav,
  NavUpload: NavUpload,
  NavEdit: NavEdit,
  getProduct: getProduct,
  getCate: getCate,
  NavCreate: NavCreate,
  DelCate: DelCate,
  CateEdit: CateEdit,
  CateCreate: CateCreate,
  DelProduct: DelProduct,
  ProductEdit: ProductEdit,
  ChangeShelf: ChangeShelf,
  ProductCreate: ProductCreate,
  domain:domain
}
