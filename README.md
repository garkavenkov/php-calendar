# Сalendar
Calendar

## Installation

Use [Composer](https://getcomposer.org "Composer")

>composer require garkavenkov/calendar

## Usage

### Initialization

```php

use Garkavenkov\Calendar\Calendar;

$cldr = new Calendar();
```

This code creates calendar based on current date. If you need to create calendar on particular month and year, pass parameters to class constructor

```php

$cldr = new Calendar(year: 2022, month: 10);
```

By default calendar creates with week begins on Sunday. If you need week begin on Monday, pass next parameter to class constructor

```php
$cldr = new Calendar(year: 2022, month: 10, week_begins_on_monday:true);
```

Also you can set language for day's and month's names. In this you need to pass additional parameter to constructor

```php
$cldr = new Calendar(year: 2022, month: 10, week_begins_on_monday:true, lang: 'ua');
```

### Calendar structure

To get calendar use command:

```php
$cldr = new Calendar(year: 2022, month: 10, week_begins_on_monday:true, lang: 'ua');
$cal = $cldr->get();
```

The calendar has the following structure:

```
Array
(
    [info] => Array
        (
            [weekDayNames] => Array
                (
                    [0] => Понеділок
                    [1] => Вівторок
                    [2] => Середа
                    [3] => Четвер
                    [4] => П`ятниця
                    [5] => Субота
                    [6] => Неділя
                )
            [months] => Array
                (
                    [0] => Січень
                    [1] => Лютий
                    [2] => Березень
                    [3] => Квітень
                    [4] => Травень
                    [5] => Червень
                    [6] => Липень
                    [7] => Серпень
                    [8] => Вересень
                    [9] => Жовтень
                    [10] => Листопад
                    [11] => Грудень
                )
            [month] => Array
                (
                    [index] => 10
                    [name] => Жовтень
                )
            [year] => 2022
        )
    [weeks] => Array
        (
            [0] => Array
                (
                    [number] => 39
                    [days] => Array
                        (
                            [0] => Array
                                (
                                    [date] => 2022-09-26
                                    [mday] => 26
                                    [wday] => 1
                                    [mon] => 9
                                    [year] => 2022
                                    [yday] => 268
                                    [weekday] => Понеділок
                                    [month] => Вересень
                                )

                             ...
                            [6] => Array
                                (
                                    [date] => 2022-10-02
                                    [mday] => 2
                                    [wday] => 0
                                    [mon] => 10
                                    [year] => 2022
                                    [yday] => 274
                                    [weekday] => Неділя
                                    [month] => Жовтень
                                )

                        )
                )

            ...

            [5] => Array
                (
                    [number] => 44
                    [days] => Array
                        (
                            ...
                            [6] => Array
                                (
                                    [date] => 2022-11-06
                                    [mday] => 6
                                    [wday] => 0
                                    [mon] => 11
                                    [year] => 2022
                                    [yday] => 309
                                    [weekday] => Неділя
                                    [month] => Листопад
                                )
                        )
                )
        )
) 
```

## Methods

#### getCalendarBoundries($format)

If you need to get calendar first and last day, use folowing code:

```php

$boundries = $cal->getCalendarBoundries($format = null);
``` 
This method returns an array of calendar start and end dates

```
Array
(
    [0] => Array
        (
            [date] => 2022-09-26
            [mday] => 26
            [wday] => 1
            [mon] => 9
            [year] => 2022
            [yday] => 268
            [weekday] => Понеділок
            [month] => Вересень
        )

    [1] => Array
        (
            [date] => 2022-11-06
            [mday] => 6
            [wday] => 0
            [mon] => 11
            [year] => 2022
            [yday] => 309
            [weekday] => Неділя
            [month] => Листопад
        )

)
```

You can pass $format variable into method.

```php

$boundries = $cal->getCalendarBoundries('Y-m-d');

``` 
With this format methods will return array with formated date 

```
Array
(
    [0] => 2022-26-09
    [1] => 2022-06-11
)

```

#### getMonthBoundries($format = null)

Method `getMonthBoundries()` returns array with first and last day of the calendar month

```php

$boundries = $cal->getMonthBoundries();
```

```
Array
(
    [0] => Array
        (
            [date] => 2022-10-01
            [mday] => 1
            [wday] => 6
            [mon] => 10
            [year] => 2022
            [yday] => 273
            [weekday] => Субота
            [month] => Жовтень
        )

    [1] => Array
        (
            [date] => 2022-10-31
            [mday] => 31
            [wday] => 1
            [mon] => 10
            [year] => 2022
            [yday] => 303
            [weekday] => Понеділок
            [month] => Жовтень
        )

)
```

Likewise `getCalendarBoundries()` you can pass a `$format` variable to a method and get the formatted dates

```php

$boundries = $cal->getMonthBoundries('Y-m-d');
```

```
Array
(
    [0] => 2022-10-01
    [1] => 2022-10-31
)
```

#### getWeekdays()

Method `getWeekdays()` returns an array containing the names of the days of the week.

```php
$days = $cal->getWeekdays();
print_r($days);
```

```
Array
(
    [0] => Понеділок
    [1] => Вівторок
    [2] => Середа
    [3] => Четвер
    [4] => П`ятниця
    [5] => Субота
    [6] => Неділя
)
```

#### getMonths()

Method `getMonths()` returns an array containing the names of the months.

```php
$months = $cal->getMonths();
print_r($months);
```

```
Array
(
    [0] => Січень
    [1] => Лютий
    [2] => Березень
    [3] => Квітень
    [4] => Травень
    [5] => Червень
    [6] => Липень
    [7] => Серпень
    [8] => Вересень
    [9] => Жовтень
    [10] => Листопад
    [11] => Грудень
)
```

#### injectIntoDay(string \$title, array $events) 

This method allows to inject data into a day with particular `$title`. For example, there is a set of data `$events` containing date and some information e.g. 
```
$events = array(
    ['date' => '2022-10-01', 'name' => 'todo1',
    ['date' => '2022-10-01', 'name' => 'todo2',
    ['date' => '2022-10-02', 'name' => 'todo1',
    ['date' => '2022-10-02', 'name' => 'todo2',    
);

....
```
Following code will inject this dataset into calendar with title `todos`

```php
$cal->injectIntoDay('todos', $events);
print_t($cal->get());
```

output

```
...
    [5] => Array
        (
            [date] => 2022-10-01
            [mday] => 1
            [wday] => 6
            [mon] => 10
            [year] => 2022
            [yday] => 273
            [weekday] => Субота
            [month] => Жовтень
            [todos] => Array
                (
                    [0] => Array
                        (
                            [date] => 2022-10-01
                            [name] => todo1
                        )
                    [1] => Array
                        (
                            [date] => 2022-10-01
                            [name] => todo2
                        )
                )
        )
    [6] => Array
        (
            [date] => 2022-10-02
            [mday] => 2
            [wday] => 0
            [mon] => 10
            [year] => 2022
            [yday] => 274
            [weekday] => Неділя
            [month] => Жовтень
            [todos] => Array
                (
                    [0] => Array
                        (
                            [date] => 2022-10-02
                            [name] => todo1
                        )
                    [1] => Array
                        (
                            [date] => 2022-10-02
                            [name] => todo2
                        )
                )
        )
    ...
```

#### getWeeksNumbers()

This method returns an array containing calendar's weeks numbers.

```php
$numbers = $cal->getWeeksNumbers();
print_r($numbers);
```
output
```
Array
(
    [0] => 39
    [1] => 40
    [2] => 41
    [3] => 42
    [4] => 43
    [5] => 44
)
```

#### getWeek(int $number)

This method returns an array containing calendar's week by number `$number`.

```php
$week = $cal->getWeek(40);
print_r($week);
```
output
```
Array
(
    [1] => Array
        (
            [number] => 40
            [days] => Array
                (
                    [0] => Array
                        (
                            [date] => 2022-10-03
                            [mday] => 3
                            [wday] => 1
                            [mon] => 10
                            [year] => 2022
                            [yday] => 275
                            [weekday] => Понеділок
                            [month] => Жовтень
                        )
                   ...
                    [6] => Array
                        (
                            [date] => 2022-10-09
                            [mday] => 9
                            [wday] => 0
                            [mon] => 10
                            [year] => 2022
                            [yday] => 281
                            [weekday] => Неділя
                            [month] => Жовтень
                        )
                )
        )
)
```

#### getCalendarInfo(string $dateFormat = null)

This method returns an array containing basic information about calendar, i.e. month, year, calendar's  and month's boundries

```php
$info = $cal->getCalendarInfo();
print_r($info);
```
output
```
Array
(
    [year] => 2022
    [month] => Array
        (
            [index] => 10
            [name] => Жовтень
        )
    [calendarBoundries] => Array
        (
            [0] => Array
                (
                    [date] => 2022-09-26
                    [mday] => 26
                    [wday] => 1
                    [mon] => 9
                    [year] => 2022
                    [yday] => 268
                    [weekday] => Понеділок
                    [month] => Вересень
                )
            [1] => Array
                (
                    [date] => 2022-11-06
                    [mday] => 6
                    [wday] => 0
                    [mon] => 11
                    [year] => 2022
                    [yday] => 309
                    [weekday] => Неділя
                    [month] => Листопад
                )
        )
    [monthBoundries] => Array
        (
            [0] => Array
                (
                    [date] => 2022-10-01
                    [mday] => 1
                    [wday] => 6
                    [mon] => 10
                    [year] => 2022
                    [yday] => 273
                    [weekday] => Субота
                    [month] => Жовтень
                )
            [1] => Array
                (
                    [date] => 2022-10-31
                    [mday] => 31
                    [wday] => 1
                    [mon] => 10
                    [year] => 2022
                    [yday] => 303
                    [weekday] => Понеділок
                    [month] => Жовтень
                )
        )
)
```

It is possible to output calendar's and month's boundries in particular format. To do this, you need to pass a parameter with the necessary format


```php
$info = $cal->getCalendarInfo(format: 'Y-m-d');
print_r($info);
```

output
```
Array
(
    [year] => 2022
    [month] => Array
        (
            [index] => 10
            [name] => Жовтень
        )
    [calendarBoundries] => Array
        (
            [0] => 2022-09-26
            [1] => 2022-11-06
        )
    [monthBoundries] => Array
        (
            [0] => 2022-10-01
            [1] => 2022-10-31
        )
)
```

#### getDay(string $date)

This method returns an array containing particular day information

```php
$day = $cal->getDay('2022-10-22');
print_r($day);
```
output

```
Array
(
    [0] => Array
        (
            [date] => 2022-10-22
            [mday] => 22
            [wday] => 6
            [mon] => 10
            [year] => 2022
            [yday] => 294
            [weekday] => Субота
            [month] => Жовтень
        )
)
```