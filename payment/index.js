const Hapi  = require('hapi');
const sleep = require('sleep');

const server = new Hapi.Server({
    cache: { engine: require('catbox-memory') }
});

server.connection({port: process.env.PORT || 8000});
server.register({
    register: require('hapi-rate-limit'),
    options: {
        pathLimit: 10,
        userLimit: 20,
        userCache: {
            expiresIn: 1000 * 10 // 10 secs
        }
    }
});

server.route({
    method: 'POST',
    path: '/pay',
    config: {
        handler: function (request, reply) {
            sleep.sleep(1)
            reply({
                status: 'success',
            }).code(200);
        }
    }
});

server.start((err) => {
    if (err) {
        throw err;
    }
    console.log(`Server running at: ${server.info.uri}`);
});
