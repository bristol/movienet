create temporary table steves as (select SQL_NO_CACHE pid from people where name like 'Spielberg, Steven');
create temporary table steve_movies as (select SQL_NO_CACHE distinct mid from directed where pid in steves);
create temporary table steve_actors as (select SQL_NO_CACHE distinct pid from acted_in where mid in steve_movies);
select SQL_NO_CACHE * from people where pid in steve_actors;
