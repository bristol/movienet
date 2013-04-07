create temporary table steves as (select pid from people where name like 'Spielberg, Steven');
create temporary table steve_movies as (select distinct mid from directed where pid in steves);
create temporary table steve_actors as (select distinct pid from acted_in where mid in steve_movies);
select * from people where pid in steve_actors;
