# 如何安装？

### 克隆项目

```shell
$ git clone https://github.com/siganushka/api-starter-kit.git
$ cd ./api-starter-kit
```

### 配置参数

```shell
$ composer dump-env {ENV} # ENV 为当前环境，可选为 dev, test, prod
```

> 打开 ``.env.local.php`` 文件修改项目所需参数，比如数据库信息

### 安装项目

```shell
$ composer install
```

### 创建数据库

```shell
$ php bin/console doctrine:database:create # 创建数据库
$ php bin/console doctrine:schema:update --force # 创建表结构
$ php bin/console doctrine:fixtures:load # 生成测试数据（可选）
```

### 前端依赖

```shell
$ yarn install # 安装前端依赖
$ yarn encore production # 打包压缩前端依赖（javascript, css, img...）
```

### 单元测试

```shell
$ cp .env.test .env.test.local # 复制测试本地环境变量
```

> 打开 ``.env.test.local`` 文件修改测试所需参数

```shell
$ php bin/phpunit --debug # 执行单元测试
```

### 生成接口文档

```shell
$ ./node_modules/.bin/apidoc -i ./src/Controller/ -o ./public/apidoc
```

> 打开  ``http://{HOST}/apidoc/index.html`` 查看接口文档
