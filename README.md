# 后端restful格式接口框架



## 测试用例使用方式
```
 php vendor/bin/phpunit app/test
```
## swagger 
生成文档
```
 php vendor/bin/swagger app/
```

## nginx 配置
```
server {
        listen       80;
        server_name     be.xxx.com;
        set $root {$dir}/platform-be;
        root $root;
        index index.php;

        location /api {
                try_files $uri /index.php$is_args$args;
        }

        location /swagger.json {
                add_header 'Access-Control-Allow-Methods' 'GET, POST, DELETE, PUT';
                add_header 'Access-Control-Allow-Origin' '*';
                add_header 'Access-Control-Allow-Credentials' 'true';
                add_header 'Access-Control-Allow-Headers' 'DNT,X-CustomHeader,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type';
                root $root;
        }

        location ~ \.php {
                if ($request_method = 'OPTIONS') {
                        add_header 'Access-Control-Allow-Methods' 'GET, POST, OPTIONS, DELETE, UPDATE, PUT';
                        add_header 'Access-Control-Allow-Origin' '.domain.com';
                        add_header 'Access-Control-Allow-Headers' 'DNT,X-CustomHeader,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type';
                        add_header 'Access-Control-Max-Age' 1728000;
                        add_header 'Access-Control-Allow-Credentials' 'true';
                        add_header 'Content-Type' 'text/plain charset=UTF-8';
                        add_header 'Content-Length' 0;
                        return 204;
                }
                if ($request_method = 'POST') {
                        add_header 'Access-Control-Allow-Methods' 'GET, POST, DELETE, PUT';
                        add_header 'Access-Control-Allow-Origin' '.domain.com';
                        add_header 'Access-Control-Allow-Credentials' 'true';
                        add_header 'Access-Control-Allow-Headers' 'DNT,X-CustomHeader,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type';
                }
                if ($request_method = 'GET') {
                        add_header 'Access-Control-Allow-Methods' 'GET, POST, DELETE, PUT';
                        add_header 'Access-Control-Allow-Origin' '.domain.com';
                        add_header 'Access-Control-Allow-Credentials' 'true';
                        add_header 'Access-Control-Allow-Headers' 'DNT,X-CustomHeader,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type';
                }
                if ($request_method = 'DELETE') {
                        add_header 'Access-Control-Allow-Methods' 'GET, POST, DELETE, PUT';
                        add_header 'Access-Control-Allow-Origin' '.domain.com';
                        add_header 'Access-Control-Allow-Credentials' 'true';
                        add_header 'Access-Control-Allow-Headers' 'DNT,X-CustomHeader,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type';
                }
                if ($request_method = 'PUT') {
                        add_header 'Access-Control-Allow-Methods' 'GET, POST, DELETE, PUT';
                        add_header 'Access-Control-Allow-Origin' '.domain.com';
                        add_header 'Access-Control-Allow-Credentials' 'true';
                        add_header 'Access-Control-Allow-Headers' 'DNT,X-CustomHeader,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type';
                }
                root    {dir}/platform-be;
                include fastcgi_params;
                fastcgi_pass  127.0.0.1:5555;
                fastcgi_index  index.php;
                fastcgi_param  ENVIRONMENT production;
                fastcgi_param  SCRIPT_FILENAME  $root/public/index.php;
        }

        access_log  logs/$server_name.log  main;
}
```


