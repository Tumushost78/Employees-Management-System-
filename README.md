# Employees-Management-System

```php
<?php

class User {
    private string \$name;

    public function __construct(string \$name) {
        this->name = name;
    }

    public function greet(): string {
        return "Hello, " . \$this->name;
    }
}

\$user = new User("Alice");
echo \$user->greet();
```

