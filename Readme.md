Exception
===

- Это реализация хранения исключений ориентирована на файловую систему.
- Нужно класть файлы исключений в директорию Exception.
- И нужно регистрировать новые исключения в общем enum со списком всех возможных значений.
- Это позволит автоматически калькулировать строковый и числовой код ошибки.
- К тому же этот подход диктует правила работы с исключениями, что сделает их хранение и использование более чистым

```php
// Текстовый код ошибки.
// Калькулируется по расположению в файловой системе.
// Нужен, чтобы завязать на него систему переводов
$e->getCodeText(); 

// Числовой код ошибки.
// Калькулируется по позиции в enum.
// Нужен чтобы клиенту было легче сообщить об ошибке
$e->getCode(); 

// Штатное поле. Не менялось
$e->getMessage();

// Здесь getMessage.
// Если он пустой, то текст будет на основе названия исключения. 
$e->getSummary();
```

```php
use \Rinsvent\Exception\AbstractException;
use \Rinsvent\Exception\CodeTrait;

class DefaultException extends AbstractException {} // psth: src/Exception/DefaultException.php
class AccessDenied extends AbstractException {} // psth: src/Exception/AccessDenied.php
class AlreadyCreated extends AbstractException {} // psth: src/Exception/User/AlreadyCreated.php

enum MyProjectEnum: string implements \Rinsvent\Exception\CodeInterface
{
    use CodeTrait;

    case Default = DefaultException::class;
    case AccessDenied = AccessDenied::class;
    case UserAlreadyCreated = AlreadyCreated::class;
}

// Регистрируем наше хранилище с ошибками
AbstractException::$exceptionEnum = MyProjectEnum::class;

$e = new AlreadyCreated('Your custom message');
$e->getCodeText(); // user.already_created
$e->getCode(); // 200
$e->getMessage(); // Your custom message
$e->getSummary(); // Your custom message

$e = new AccessDenied();
$e->getCodeText(); // access_denied
$e->getCode(); // 100
$e->getMessage(); // ''
$e->getSummary(); // Access denied
```