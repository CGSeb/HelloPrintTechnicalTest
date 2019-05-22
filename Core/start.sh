docker rm -f helloprintcore
docker run -d --hostname my-rabbit --name helloprintcore -p 8080:15672 rabbitmq:3-management