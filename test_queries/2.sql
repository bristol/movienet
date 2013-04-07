create temporary table brads as (select SQL_NO_CACHE pid from people where name like 'Pitt, Brad');
create temporary table brad_movies as (select SQL_NO_CACHE distinct mid from brads natural join acted_in);
select SQL_NO_CACHE * from brad_movies natural join movies;
