Installation
------------

```
    composer require hevinci/competency-bundle dev-master
    php app/console claroline:update
```

Tests
-----

```
    php app/console claroline:init_test_schema --env=test
    phpunit -c vendor/hevinci/competency-bundle/HeVinci/CompetencyBundle
```