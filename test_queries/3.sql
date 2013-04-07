create temporary table steves as (select SQL_NO_CACHE pid from people where name like 'Spielberg, Steven');
create temporary table steve_movies as (select SQL_NO_CACHE distinct mid from steves natural join directed);
create temporary table steve_actors as (select SQL_NO_CACHE distinct pid from steve_movies natural join acted_in);
select SQL_NO_CACHE * from steve_actors natural join people;
