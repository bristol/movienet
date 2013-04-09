select SQL_NO_CACHE email
from top_five T, rated R, users U
where T.mid=R.mid and R.uid=U.uid and rating >= 9
group by U.uid 
having count(*) >= 2;
