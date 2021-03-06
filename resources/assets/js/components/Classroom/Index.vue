<template>
  <section
    class="classroom"
    :style="classroom.color ? `--default: ${classroom.color}` : ''">
    <navigator :routes="routes">
      <template>
        <i
          @click="update()"
          class="fa fa-fw fa-cog pointer"
          style="font-size: 1.5rem; margin-right: .5rem"
          v-if="admin"
          v-tooltip="'Atualizar classe'" />
        <span v-tooltip.bottom="'Alternar cor do curso'"
          @click="color()"
          v-if="admin"
          class="pointer color-picker" />
      </template>
    </navigator>
    <div class="scroll">
      <article class="container main">
        <side-users
          :masters="masters"
          :users="classroom.users"
          class="aside" />
        <main-board :admin="admin" class="main"></main-board>
        <side-menu-classroom
          :masters="masters"
          :users="classroom.users || []"/>
      </article>
    </div>
  </section>
</template>

<script>
import Navigator from '../commons/Navigator.vue'
import ColorPicker from './Modal/ColorPicker.vue'
import SideUsers from './SideUsers.vue'
import SideMenuClassroom from './SideMenuClassroom.vue'
import MainBoard from './MainBoard.vue'
import mixin from '../../mixins/index'
import FormClassroom from './Modal/FormClassroom.vue'
import { EventBus } from '../../modules/bus'
let fromStore = name => {
  return function () {
    return this.$store.getters[name]
  }
}

let created = function () {
  let id = this.$route.params.id
  let classroom = this.user.classrooms.find(it => it.id === id)
  this.$store.dispatch('classroom', classroom)
  this.routes = []
  this.routes.push({
    icon: 'home',
    text: this.classroom.name,
    route: `/classroom/${classroom.id}`
  })
  let promise = this.$http.get(`/api/classroom/${classroom.id}`, {
    params: { users: true }
  })
  promise
    .then(result => {
      let data = result.body.data
      this.$store.dispatch('classroom', data.classroom)
    })
    .catch(err => {
      this.err = err
    })
  EventBus.$on('user:assign', ({ user, role }) => {
    this.classroom.users.find(it => it.id === user).role = role
  })
  if (this.postable) {
    this.routes.push({
      icon: 'file-text',
      text: 'Publicação',
      route: `/classroom/${classroom.id}/${this.postable}`
    })
  }
  EventBus.$on('route:push', route => {
    this.routes.push(route)
  })
}

export default {
  name: 'Classroom',
  created,
  mixins: [mixin],
  components: {
    Navigator,
    SideUsers,
    SideMenuClassroom,
    MainBoard
  },
  data: _ => ({
    routes: [],
    err: null
  }),
  methods: {
    // Atualiza classe
    submit (params) {
      this.$http.put('/api/classroom/' + this.classroom.id, params)
        .then(data => {
          this.$modal('close')
          this.$store.dispatch('updateClassroom', data.body.data.classroom)
        })
        .catch(e => {
          this.$modal('close')
          this.$toastr.e('Erro ao atualizar classe')
        })
    },
    color () {
      let color = this.$store.getters.classroom.color
      let onClose = () => {
        this.$modal('close')
        if (color === this.$store.getters.classroom.color) {
          return
        }
        this.$store.dispatch('color', {
          color: this.$store.getters.classroom.color,
          code: this.$store.getters.classroom.code,
          save: true
        })
      }
      this.$modal({
        component: ColorPicker,
        onClose,
        class: 'min'
      })
    },
    update () {
      this.$modal({
        component: FormClassroom,
        onClose: true,
        data: this.classroom
      })
    }
  },
  mounted () {
    // this.$nextTick(_ => this.open())
  },
  destroyed () {
    this.$store.dispatch('destroy', null)
  },
  computed: {
    user: fromStore('user'),
    classroom: function () {
      let cl = this.$store.getters.classroom
      if (cl && this.routes.length) {
        this.routes[0].text = cl.name
      }
      return cl
    },
    admin: function () {
      return this.masters.some(item => item.id === this.user.id)
    },
    masters: function () {
      if (!this.classroom.users) {
        return []
      }
      return this.classroom.users.filter(user => !!user.role.match(this.regexMaster))
    },
    postable () {
      return this.$route.params.post
    }
  }
}
</script>

<style lang="sass" scoped>
  .container.main
    justify-content: center
    > *
      &:nth-child(1)
        flex: 0 1 10%
      &:nth-child(2)
        flex: 0 1 50%
      &:nth-child(3)
        flex: 0 1 30%
  .classroom >>> nav
    margin-bottom: 0
  .scroll
    padding: 1.4rem
    overflow: auto
  .scroll, .classroom
    flex: 1
  .classroom, .container
    display: flex
    flex-direction: column
  .container
    flex-direction: row
  .main
    flex: 1
  .color-picker
    --s: 2rem
    background-color: var(--default)
    border-radius: 50%
    box-shadow: 0 0 3px #ccc
    height: var(--s)
    width: var(--s)
</style>
