create temporary table derricks as (select SQL_NO_CACHE uid from users where name like 'DERRICK MYERS' and age=36);
create temporary table derrick_movies as (select SQL_NO_CACHE distinct mid from rated where rating=10 and uid in derricks);
create temporary table derrick_directors as (select SQL_NO_CACHE distinct pid from directed where mid in derrick_movies);
select SQL_NO_CACHE name from people where pid in derrick_directors;
