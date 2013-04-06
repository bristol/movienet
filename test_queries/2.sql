create temporary table brads as (select SQL_NO_CACHE pid from people where name like 'Pitt, Brad');
create temporary table brad_movies as (select SQL_NO_CACHE distinct mid from acted_in where pid in brads);
select SQL_NO_CACHE * from movies where mid in brad_movies;
