const HAPI          = require("hapi")
const HAPIWebSocket = require("hapi-plugin-websocket")
const uuidv4        = require('uuid/v4')
const Boom          = require('boom');

let server = new HAPI.Server()
server.connection({port: process.env.PORT || 8001})
server.register(HAPIWebSocket)

let channels = {};

/*  combined REST/WebSocket route  */
server.route({
    method: "POST", path: "/open",
    config: {
        payload: { output: "data", parse: true, allow: "application/json" },
        plugins: { 
          websocket: {
            initially: true,
            connect: ({ ctx, ws }) => {
              ctx.id = uuidv4()
              channels[ctx.id] = ws
            },
            disconnect: ({ ctx }) => {
              channels[ctx.id] = null
            }
          }
        }
    },
    handler: (request, reply) => {
        let { initially, ws, ctx } = request.websocket()
        if (initially) {
          ws.send(JSON.stringify({id: ctx.id}))
        }
    }
})

/*  plain REST route  */
server.route({
  method: "POST", path: "/notify/{id}",
  config: {
      payload: { output: "data", parse: true, allow: "application/json" }
  },
  handler: (request, reply) => {
      const ws = channels[request.params.id]
      
      if (ws){
        ws.send(JSON.stringify(request.payload))
        return reply({ sent: "OK"})
      }
      
      return reply(Boom.notFound('Channel not found'))
  }
})
server.start()