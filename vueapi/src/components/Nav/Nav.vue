<template>
  <div class="Nav">
    <a-table :dataSource="data" :pagination="false">
      <a-table-column title="id" key="id" dataIndex="id"></a-table-column>
      <a-table-column title="位置" key="positionId" dataIndex="positionId"></a-table-column>
      <a-table-column title="名称" key="title" dataIndex="title"></a-table-column>
      <a-table-column title="图片" key="picture" dataIndex="picture">
        <template slot-scope="picture,id">
          <img :src="picture" width="50">
        </template>
      </a-table-column>
      <a-table-column title="链接类型" key="linkType" dataIndex="linkType"></a-table-column>
      <a-table-column title="链接目标" key="linkTarget" dataIndex="linkTarget"></a-table-column>
      <a-table-column title="创建时间" key="created" dataIndex="created"></a-table-column>
      <a-table-column title="修改时间" key="updated" dataIndex="updated"></a-table-column>
      <a-table-column title="操作" key="operation" dataIndex="operation">
        <template slot-scope="operation,id">
          <a-popconfirm
            v-if="data.length"
            title="确定删除?"
            @confirm="() => onDelete(id)"
          >
            <a-button type="danger">删除</a-button>
          </a-popconfirm>
          <a-button type="primary" @click="goToEdit(id.id)">编辑</a-button>
        </template>
      </a-table-column>
    </a-table>
    <a-pagination :pageSize="pageSize" @change="PageChange" :defaultCurrent="1" :total="total"/>
  </div>
</template>

<script>
    import moment from 'moment';
    import qs from 'qs';
    import Api from '../../Api';

    export default {
        inject: ['reload'],
        data() {
            return {
                pageSize: 0,
                total: 0,
                pagination: {},
                //位置
                positionList: {
                    "1": "顶部导航",
                    "2": "banner图",
                    "3": "icon",
                    "4": "4张大图",
                },
                //链接类型
                linkType: {
                    "1": "商品分类页面",
                    "2": '商品购买页面',
                    "3": '商品活动页面',
                    "4": '店铺',
                },
                statusId: [],
                data: []
            }
        },
        methods: {
            PageChange(page, pageSize) {
                this.setData(page);
            },
            setData(page = '') {
                return new Promise((resolve, reject) => {
                    this.axios.get(Api.getNav + `?page=${page}`).then((res) => {
                        if (res.data.status) {
                            this.data = [];
                            res.data.data.data.forEach((val, key) => {
                                var obj = {
                                    key: val.id,
                                    id: val.id,
                                    positionId: this.positionList[val.position_id],
                                    title: val.title,
                                    picture: val.picture,
                                    linkType: this.linkType[val.link_type],
                                    linkTarget: val.link_target,
                                    created: moment().format(val.created_at, 'YYYY-MM-DD HH:mm:ss'),
                                    updated: moment().format(val.updated_at, 'YYYY-MM-DD HH:mm:ss'),
                                }

                                this.data.push(obj);
                            });

                            resolve(this.total = res.data.data.total, this.pageSize = res.data.data.per_page);
                        }
                    });
                });
            },
            onDelete(id) {
                let data = qs.stringify({
                    "id": id.id,
                });
                this.axios.post(Api.DelNav, data).then((data) => {
                    if (data.data) {
                        const dataSource = [...this.data];
                        this.data = dataSource.filter(item => item.key !== id.id);
                        this.$message.info('删除成功');
                    } else {
                        this.$message.error('删除失败');
                    }
                });
            },
            goToEdit(id) {
                this.$router.push({path: '/NavEdit', query: {id: id}});
            }
        },
        created() {
            this.setData();
        }
    }
</script>

<style lang="scss">
  .highlight {
    background-color: rgb(255, 192, 105);
    padding: 0px;
  }

  td {
    text-align: left;
  }
</style>
