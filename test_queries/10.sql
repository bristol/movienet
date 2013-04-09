create temporary table good_users as (select SQL_NO_CACHE U.uid
					from top_five T, rated R, users U
					where T.mid=R.mid and R.uid=U.uid and rating >= 9
					group by U.uid
					having count(*) >= 2);
create temporary table good_movies as (select SQL_NO_CACHE R.mid, avg(R.rating)
					from good_users U, rated R
					where U.uid=R.uid
					group by R.mid
					having count(*) > 2);
select SQL_NO_CACHE M.title, M.year, G.`avg(R.rating)`
from good_movies G, movies M
where G.mid=M.mid
order by G.`avg(R.rating)` desc limit 10;
