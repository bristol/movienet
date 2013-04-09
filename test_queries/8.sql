create temporary table movie_avgs as (select SQL_NO_CACHE mid, avg(rating) from rated group by mid);
create table if not exists top_five select SQL_NO_CACHE * from movie_avgs order by `avg(rating)` desc limit 5;
select SQL_NO_CACHE title, year, `avg(rating)` from top_five natural join movies;
