<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

-   [Simple, fast routing engine](https://laravel.com/docs/routing).
-   [Powerful dependency injection container](https://laravel.com/docs/container).
-   Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
-   Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
-   Database agnostic [schema migrations](https://laravel.com/docs/migrations).
-   [Robust background job processing](https://laravel.com/docs/queues).
-   [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 2000 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

-   **[Vehikl](https://vehikl.com/)**
-   **[Tighten Co.](https://tighten.co)**
-   **[WebReinvent](https://webreinvent.com/)**
-   **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
-   **[64 Robots](https://64robots.com)**
-   **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
-   **[Cyber-Duck](https://cyber-duck.co.uk)**
-   **[DevSquad](https://devsquad.com/hire-laravel-developers)**
-   **[Jump24](https://jump24.co.uk)**
-   **[Redberry](https://redberry.international/laravel/)**
-   **[Active Logic](https://activelogic.com)**
-   **[byte5](https://byte5.de)**
-   **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

Criar o arquivo Request com validações
``

php artisan make:request ContaRequest

`` Pesquisado por seeder na documentação do Laravel
php artisan make:seeder UserSeeder -
Depois executar este comando: php artisan db:seed

`` Comando usado para traduzir para portugues.
Link: https://github.com/lucascudo/laravel-pt-BR-localization

1.  php artisan lang:publish

2.  composer require lucascudo/laravel-pt-br-localization --dev

3.  php artisan vendor:publish --tag=laravel-pt-br-localization

4.  Vai no config/app.php : ''locale' => 'pt_BR'

`` Pesquisar por QueryString
e ver esta funcao: ->withQueryString();

`` Instalamos o DOM PDF
Link: https://github.com/barryvdh/laravel-dompdf

1. composer require barryvdh/laravel-dompdf
2.

`` INSTALAR O SWEET ALERT:
DOWNLOAD & INSTALL:

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

npm install sweetalert2

depois de executar o npm sweetalert, vai na pasta public, no ficheiro bootstrap.js e importamos lá o sweetalert.

## Criar Seeder

php artisan make:seeder SituacaoContaSeeder

para a execução: php artisan db:seed

## usamos a biblioteca do Select2:

Link: https://select2.org/

Teremos que incluir um JQuery na pesquisa, eis o Link:

utilizamos uma CDN do google: https://developers.google.com/speed/libraries?hl=pt-br#jquery

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

## Como Gerar Excel com Laravel

## como Gerar Word com Laravel

Link da documentação:
https://github.com/PHPOffice/PHPWord

Instalação da dependencia para gerar word: composer require phpoffice/phpword

## Criar a logica do registro do banco de dados

criamos uma migration com as seguintes configurações:

return new class extends Migration
{
/\*\*
_ Run the migrations.
_/
public function up(): void
{
Schema::table('contas', function (Blueprint $table) {
$table->softDeletes()->after('updated_at');
});
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contas', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }

};

depois dessa configuração fizemos uma migration e verificamos que acresce o campo delete_id na tabela conta.
