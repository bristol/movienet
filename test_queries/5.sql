create temporary table derricks as (select SQL_NO_CACHE uid from users where name like 'DERRICK MYERS' and age=36);
create temporary table derrick_movies as (select SQL_NO_CACHE mid from derricks natural join rated where rating=10);
create temporary table derrick_directors as (select SQL_NO_CACHE pid from derrick_movies natural join directed);
select SQL_NO_CACHE name from derrick_directors natural join people; 
