create temporary table derricks as (select uid from users where name like 'DERRICK MYERS' and age=36);
create temporary table derrick_movies as (select distinct mid from rated where rating=10 and uid in derricks);
create temporary table derrick_directors as (select distinct pid from directed where mid in derrick_movies);
select name from people where pid in derrick_directors;
