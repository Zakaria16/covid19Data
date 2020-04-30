# covid19Data
Developers get a constantly updated data in JSON format on COVID-19 cases (including confirmed cases, death cases and recovered cases ) both local(Ghana) and global data with this endpoint:
- version 1 [https://mazitekgh.com/covid19/v1/](https://mazitekgh.com/covid19/v1/)
- version 2 [https://mazitekgh.com/covid19/v2/](https://mazitekgh.com/covid19/v2/)

## version 2
The version two provides statistics for many countries.
endpoint: `GET` [https://mazitekgh.com/covid19/v2/](https://mazitekgh.com/covid19/v2?loc=country_name)
 #### parameters:
 
|param|description|
|-----|-----------|
|loc|the country you want to retrieve its data|

if no parameter is provided it will return the same output as Version 1

 #### Example:
 To get the stats for a particular country just send a `GET` request to the v2 endpoint with parameter ?loc=country_name
 if the country name contains spaces you can replace the space with and underscore(_).
  
  
For example say to retrieve stats  for a country say **Burkina Faso**:
 ```
 curl --location --request GET 'https://mazitekgh.com/covid19/v2?loc=burkina_faso'
```
 
 Result:
  ```json
            {
                "country": "burkina faso",
                "existing": 100,
                "confirmed": 641,
                "recovered": 498,
                "deaths": 43,
                "date": "30th April, 2020",
                "time": "18:16"
            }
``` 
 for **Togo:** `curl --location --request GET 'https://mazitekgh.com/covid19/v2?loc=togo'`
 
 for **United Kingdom:** `curl --location --request GET 'https://mazitekgh.com/covid19/v2?loc=uk'`   
 or `curl --location --request GET 'https://mazitekgh.com/covid19/v2?loc=united_kingdom'`
 
 #### errors:
 country not found error:
 
```json
{
    "error": "location not found"
}
``` 

 ## version 1
Developers get a constantly updated data in JSON format on COVID-19 cases (including confirmed cases, death cases and recovered cases ) both local(Ghana) and global data with this endpoint:
version 1 endpoint [https://mazitekgh.com/covid19/v1/](https://mazitekgh.com/covid19/v1/)

The output format is of the form below:
```
{
    "ghana": {
        "existing": "50",
        "confirmed": "52",
        "recovered": "",
        "deaths": "2",
        "date": "24th March, 2020",
        "time": "18:26"
    },
    "global": {
        "existing": "285,081",
        "confirmed": "410,465",
        "recovered": "107,089",
        "deaths": "18,295",
        "date": "24th March, 2020",
        "time": "18:26"
    }
}
```

## Use case
Timeline and statistics of Corona cases in Ghana
[Ghana COVID data statistics:](http://noticeboard.mazitekgh.com/covid19gh/)
------------
statistics source: [wikipedia](https://en.wikipedia.org/wiki/2019%E2%80%9320_coronavirus_pandemic)
