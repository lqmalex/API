<template>
  <div class="box">
    <div class="login">
      <div class="text">登录</div>
      <a-form>
        <a-form-item label="邮箱" :label-col="{ span: 9 }" :wrapper-col="{ span: 6 }">
          <a-input
            v-model="email"
            placeholder="请输入邮箱"
            v-decorator="[
          'email',
          {
            rules: [
              {
                type: 'email',
                message: 'The input is not valid E-mail!',
              },
              {
                required: true,
                message: 'Please input your E-mail!',
              },
            ],
          },
        ]"
          />
        </a-form-item>
        <a-form-item label="密码" :label-col="{ span: 9 }" :wrapper-col="{ span: 6 }">
          <a-input
            type="password"
            v-model="password"
            placeholder="请输入密码"
          />
        </a-form-item>
        <a-form-item>
          <a-button type="primary" html-type="submit" @click="Login">
            Submit
          </a-button>
        </a-form-item>
      </a-form>
    </div>
  </div>
</template>

<script>
    import Api from "../../Api";
    import qs from 'qs';

    export default {
        data() {
            return {
                name: '',
                email: '',
                password: '',
            };
        },
        beforeCreate() {
            this.form = this.$form.createForm(this, {name: 'register'});
        },
        methods: {
            Login() {
                if (this.email == '') {
                    this.$message.error('邮箱不能为空');
                    return;
                }

                var reg = /^[A-Za-z0-9\u4e00-\u9fa5]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/;
                if (!reg.test(this.email)) {
                    this.$message.error('邮箱格式不正确');
                    return;
                }

                if (this.password == '') {
                    this.$message.error('密码不能为空');
                    return;
                }

                if (this.password.length < 6) {
                    this.$message.error('密码长度不对');
                    return;
                }

                let data = qs.stringify({
                    email: this.email,
                    password: this.password
                });

                this.axios.post(Api.Login, data, {
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'Accept': 'application/json'
                    }
                }).then((res) => {
                    if (!res.data.status) {
                        this.$message.error(res.data.msg);
                    } else {
                        localStorage.setItem('name',res.data.name);
                        localStorage.setItem('token', res.data.token);
                        this.$router.push('/');
                    }
                });
            },
            handleWebsiteChange(value) {
                let autoCompleteResult;
                if (!value) {
                    autoCompleteResult = [];
                } else {
                    autoCompleteResult = ['.com', '.org', '.net'].map(domain => `${value}${domain}`);
                }
                this.autoCompleteResult = autoCompleteResult;
            },
        },
        created() {

        }
    }
</script>

<style lang="scss">
  .text {
    color: #999999;
    font-size: 18px;
  }
</style>
