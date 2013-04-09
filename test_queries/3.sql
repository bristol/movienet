create temporary table steve_movies as (select SQL_NO_CACHE D.mid
					from people P, directed D
					where P.name like 'Spielberg, Steven' and P.pid=D.pid);
select SQL_NO_CACHE distinct P.pid, P.name
from people P, acted_in A, steve_movies M
where P.pid=A.pid and A.mid=M.mid;
