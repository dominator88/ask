### Welcome to the We_Questionnaire wiki!






**配置:**

1 运行环境配置

确保php环境安装cURL拓展
由于微信公众平台的特殊机制，要求部署该应用的web服务开放在80端口
请确保web服务器支持pathinfo模式，并配置好重写规则隐藏index.php文件名(即部署为TP URL模式为rewrite 2模式下的环境)

2 数据库配置

导入应用源码根目录下的questionnaire.sql文件到你新建的数据库中

4 应用配置

进入应用源码\Application\Common\Conf\config.php, 配置超级管理员账号和数据库
通过你的超级管理员账号进入后台 "http://你的域名/", 进入系统配置菜单, 根据你在公众平台或者测试号上的设定完成应用配置
当你确保公众平台设定和该应用中的设定一致后，请回到微信公众平台上保存一次配置以发起公众平台对应用服务器的接入验证请求


