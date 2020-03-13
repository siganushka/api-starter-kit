# 如何安装？

### 克隆项目

```bash
$ git clone https://github.com/siganushka/api-starter-kit.git
$ cd ./api-starter-kit
```

### 安装项目

```bash
$ composer install
```

### 配置参数

```bash
$ composer dump-env {ENV} # ENV 为当前环境，可选为 dev, test, prod
```

> 打开 ``.env.local.php`` 文件修改项目所需参数，比如数据库信息

### 创建数据库

```bash
$ php bin/console doctrine:database:create # 创建数据库
$ php bin/console doctrine:schema:update --force # 创建表结构
$ php bin/console doctrine:fixtures:load # 生成测试数据（可选）
```

### 前端依赖

```bash
$ yarn install # 安装前端依赖
$ yarn encore production # 打包压缩前端依赖（javascript, css, img...）
```

### 单元测试

```bash
$ php bin/phpunit --debug # 执行单元测试
```

### 生成接口文档

```bash
$ ./node_modules/.bin/apidoc -i ./src/Controller/ -o ./public/apidoc
```

> 打开  ``http://{HOST}/apidoc/index.html`` 查看接口文档
