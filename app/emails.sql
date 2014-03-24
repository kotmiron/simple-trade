SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

INSERT INTO `templates` (`id`, `name`, `created_at`, `subject`, `body`) VALUES
(1, 1,'2013-01-01 00:00:00', 'Спасибо за регистрацию!',
'<h1>Уважаемый, {{ user.getName() }}</h1>
<p>Имя пользователя: <b>{{ user.getLogin() }}</b></p>
<p>Пароль: <b>{{ password }}</b></p>
<p>Для начала работы в системе, вам необходимо дождаться подтверждения вашей регистрации.</p>
<h2>Договор в формате pdf находится по вложении.</h2>
<p>--<br/>
С уважение,<br/>
Администрация</p>'),
(2, 2, '2013-01-01 00:00:00', 'Повышение квалификации {{ company.getContent().getName() }}', 'Квалификация вашей компании {{ company.getContent().getName() }} повысилась!'),
(3, 3, '2013-01-01 00:00:00', 'Повышение квалификации {{ company.getContent().getName() }}', 'Квалификация компании {{ company.getContent().getName() }} повысилась!'),
(4, 4, '2013-01-01 00:00:00', 'Подтверждение регистрации «{{ company.getContent().getTitle() }}»', '
<h1>Уважаемый, {{ user.getName() }}</h1>
<p>Регистрация вашей компании {{ company.getContent().getTitle() }} рассмотрена администратором и подтверждена.</p>
<p>Вам открыт доступ в систему.</p>
<p>--<br/>
С уважение,<br/>
Администрация</p>'),
(5, 5, '2013-01-01 00:00:00', 'Новая новость', '
<h3>Здравствуйте</h4>
Была добавлена новая новость<br>
<h4>{{ data.title }}</h4>
{{ data.text|raw }}
<p>--<br/>
С уважение,<br/>
Администрация</p>'),
(6, 6, '2013-01-01 00:00:00', 'Ваша заявка допущена к аукциону', '
<h3>Здравствуйте</h3>
<p>Ваша заявка допущена к аукциону «{{ auction.getContent().getTitle() }}»</p>
<p>--<br/>
С уважение,<br/>
Администрация</p>'),
(7, 7, '2013-01-01 00:00:00', 'Ваша заявка на аукцион отклонена', '
<h3>Здравствуйте</h3>
<p>Ваша заявка на аукцион «{{ auction.getContent().getTitle() }}» отклонена.</p>
<p>Причина:<pre>{{ reason }}</pre></p>
<p>--<br/>
С уважение,<br/>
Администрация</p>'),
(8, 8, '2013-01-01 00:00:00', 'Восстановление пароля', '
<h3>Здравствуйте</h3>
<p>Кто-то (возможно не вы) запросил восстановление пароля<br>
После перехода по <b><a href="{{ _baseurl }}/public/restore/{{ user.createRestoreLink() }}">ссылке</a></b> вы получите новое письмо, содержащее логин и пароль для входа.</p>
<p>--<br/>
С уважение,<br/>
Администрация</p>'),
(9, 9, '2013-01-01 00:00:00', 'Новый пароль', '
<h3>Здравствуйте</h3>
<p>Успешно установлен новый пароль.</p>
<p>Имя пользователя: <b>{{ user.getLogin() }}</b></p>
<p>Пароль: <b>{{ password }}</b></p>
<p>Введите их здесь: <a href="{{ _baseurl }}">Вход в систему</a></p>
<p>--<br/>
С уважение,<br/>
Администрация</p>'),
(10, 10, '2013-01-01 00:00:00', 'Новый аукцион в вашем регионе', '
<h3>Здравствуйте</h3>
<p>В вашем регионе({{ auction.getContent().getRegion().getTitle() }}) был опубликован новый аукцион.</p>
<p>Номер извещения о проведении аукциона: {{ auction.getContent().getNoticeNumber() }}</p>
<p>Краткое название аукциона: {{ auction.getContent().getName() }}</p>
<p>Наименование организатора аукциона: {{ auction.getContent().getCompanyName() }}</p>
<hr/>
<p>Для просмотра более подробной информации и участия в аукционе перейдите по <b><a href="{{ _baseurl }}/auctions/show/{{ auction.getId() }}">ссылке</a></b></p>
<p>--<br/>
С уважение,<br/>
Администрация</p>'),
(11, 11, '2013-01-01 00:00:00', 'Шаблон договора для нового пользователя', '
<html>
  <head>
    <meta charset="utf-8">
    <title>simpleTrade</title>
  </head>
  <body>
    Компания: {{ name }} - {{ title }}<br>
    ИНН: {{ inn }}<br>
    КПП: {{ kpp }}<br>
    ОГРН: {{ ogrn }}<br>
    <h2>Сведения о лице, подписавшем Заявку</h2>
    <h3>Email:</h3> {{ email }}<br>
    <h3>Телефон:</h3> {{ phone }}<br>

  </body>
</html>'),
(12, 12, '2013-01-01 00:00:00', 'Отказ от проведения аукциона', '
<h3>Здравствуйте</h3>
<p>Компания {{ auction.getContent().getCompanyName() }} отказалась от проведения аукциона.</p>
<p>Комментарий: {{ comment }}</p>
<p>Номер извещения о проведении аукциона: {{ auction.getContent().getNoticeNumber() }}</p>
<p>Краткое название аукциона: {{ auction.getContent().getName() }}</p>
<p>Наименование организатора аукциона: {{ auction.getContent().getCompanyName() }}</p>
<hr/>
<p>--<br/>
С уважение,<br/>
Администрация</p>'),

(13, 13, '2013-01-01 00:00:00', '', '
<html>
  <head>
    <meta charset="utf-8">
    <title>simpleTrade</title>
  </head>
  <body>
<h2>Финансовый отчет</h2>
<table class="info-list-table table table-striped table-hover">
<thead><tr><th>Дата</th><th>Название компании</th><th>Пользователь</th>
<th>Договор</th><th>Вид услуги</th><th>Тип</th><th>Комментарий</th><th>Сумма</th></tr>
</thead><tbody>{% for account in accounts %}
<tr><td>{{ account.getCreatedAt() | date("d.m.Y H:i", "Europe/Moscow") }}</td>
<td>{{ account.getCompany().getContent().getTitle() }}</td>
<td>{% if account.getUser() %}{{ account.getUser().getLogin() }}{% endif %}</td>
<td>--</td>
<td>{% if account.getContent().getTariff() %}
{{ account.getContent().getTariff().getTitle() }}{% endif %}</td>
<td>{{ account.getType().getTitle() }}</td>
<td>{{ account.getContent().getComment() }}</td>
<td>{{ account.getContent().getChanges() }}</td>
</tr>{% endfor %}</tbody></table>
</body>
</html>');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
