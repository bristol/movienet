create temporary table derrick_movies as (select R.mid
						from users U, rated R
						where U.name like 'Derrick Myers' and U.age=36 and U.uid=R.uid and R.rating=10);
select SQL_NO_CACHE P.name
from derrick_movies M, directed D, people P
where M.mid=D.mid and D.pid=P.pid;
