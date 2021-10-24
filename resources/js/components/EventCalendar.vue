<template>
  <v-app id="inspire">
    <v-app-bar
      app
      color="white"
      flat
      class="d-flex justify-center mb-6"
    >
    <h1>Event Calendar</h1>
    </v-app-bar>

    <v-main class="grey lighten-3">
      <v-container>
        <v-row>
          <v-col
            cols="12"
            sm="4"
          >
            <v-card
              rounded="lg"
              class="pa-2"
            >

            <v-list-item two-line class="pa-0">
              <v-list-item-content>
                <v-list-item-title class="text-h5">
                  Event Form
                </v-list-item-title>
              <v-list-item-subtitle>sub-title</v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>


              <v-text-field
                v-model="eventForm.eventName"
                label="Event Name"
              >
              </v-text-field>
              <v-text-field 
                v-model="eventForm.fromDate"
                type="date"
                label="From Date"
              >
              </v-text-field>
               <v-text-field 
                v-model="eventForm.toDate"
                type="date"
                label="To Date"
              >
              </v-text-field>
              <v-row>
                <v-col 
                  v-for="(day) in eventForm.days" :key="day.key"
                  cols="12"
                  md="4"
                  class="py-0"
                >
                  <v-checkbox 
                v-model="day.selected"
                :label="day.text"
                 class="py-0"
              >
              </v-checkbox>
                </v-col>
              </v-row>
               <v-divider></v-divider>
              <v-card-actions>
                <v-spacer />
                <v-btn
                  dark
                  rounded
                  color="green"
                  width="100"
                  @click="storeEvents"
                >
                Save
               </v-btn>
             </v-card-actions>
            </v-card>
          </v-col>

          <v-col
            cols="12"
            sm="8"
          >
            <v-card
              rounded="lg"
              class="pa-2"
            >
              <v-list-item two-line class="pa-0">
              <v-list-item-content>
                <v-list-item-title class="text-h5">
                  {{ months[new Date().getMonth()] }}  {{ new Date().getFullYear() }}
                </v-list-item-title>
              <!-- <v-list-item-subtitle>sub-title</v-list-item-subtitle> -->
              </v-list-item-content>
            </v-list-item>

            <v-data-table
            :headers="headers"
            :items="monthCalendar"
            hide-default-footer
            disable-pagination
            >
             <template v-slot:item="{ item }">
                 <tr :class="getColor(item.event_name)">
                    <td>{{ item.dt }}</td>
                   <td>{{ item.event_name }}</td>
                </tr>
              </template>
            </v-data-table>
            </v-card>
          </v-col>

        </v-row>
    <v-snackbar
      v-model="snackbar"
      :timeout="timeout"
      color="transparent"
      top
      right
    >
      <template v-slot:action="{ attrs }">
         <v-alert
         dark
         type="success"
        text
        v-bind="attrs"
        >
              {{ message }}

          <v-btn
          dark
          text
          v-bind="attrs"
          @click="snackbar = false"
        >
          Close
        </v-btn>
      </v-alert> 

      </template>
      </v-snackbar>
        
      </v-container>
    </v-main>
  </v-app>
</template>

<script>
import axios from 'axios'

  export default {
    data: () => ({
     events: [],
     event: {
       id: 0,
       event_date: '',
       event_name: '',
       dt: '',
       day: '',
     },
     calendar: [],
     month: new Date().getMonth,
     months: [
         'January',
         'Febuary',
         'March',
         'April',
         'May',
         'June',
         'July',
         'August',
         'September',
         'October',
         'November',
         'December',
     ],
     eventForm: {
         eventName: '',
         fromDate:'',
         toDate: '',
          days: [ 
            { key: 0, text: 'Sunday', selected: false },
            { key: 1, text: 'Monday', selected: false },
            { key: 2, text: 'Tuesday', selected: false },
            { key: 3, text: 'Wednesday', selected: false }, 
            { key: 4, text: 'Thursday', selected: false },
            { key: 5, text: 'Friday', selected: false },
            { key: 6, text: 'Saturday', selected: false },
          ],
       },
       newEvents: [],
    snackbar: false,
    message: 'Event Successfully Saved.',
    timeout: 2000,
    headers: [
      { text: 'Date', value: 'dt' },
      { text: 'Event Name', value: 'event_name' }
    ]
    }),
    computed: {
        daysInMonth () {
            var dt = new Date();
            var month = dt.getMonth() + 1;
            var year = dt.getFullYear();

            var n = new Date(year, month, 0).getDate();
            return n;
        },
        monthCalendar () {
          return this.calendar
        },
      parsedDirection () {
        return this.direction.split(' ')
      },
    },
    async created () {
      var dt = new Date()
      var month = dt.getMonth()
      var year = dt.getFullYear()
      this.fromDate = this.formatDate(new Date(year, month, 1))
      this.toDate = this.formatDate(new Date(year, month + 1, 0))
      await this.getEvents()
     this.getMonthCalendar()
    },
    methods: {
        async storeEvents () {
           await axios.post('api/storeEvents', this.eventForm)
            .then(() => {
              this.getEvents()
                .then(() => {
                  this.calendar = []
                  this.getMonthCalendar()
                })
            this.snackbar = true
          })
          .catch((error) => {
            console.log('error:', error)
          })
        },
        async getEvents () {
          await axios.get('api/getEvents')
        .then((response) => {
            this.events = response.data
            console.log('events:', this.events)
        })
        .catch((error) => {
            console.log('error:', error)
        })
        },
        getMonthCalendar () {
           var dt = new Date();
           var month = dt.getMonth();
           var year = dt.getFullYear();
           var noOfDays = new Date(year, month + 1, 0).getDate();

           
           for (let d = 1; d <= noOfDays; d++)
           {
             var dd = new Date (year, month, d)
             var event = {
               event_date: dd,
               event_name: this.getEvent(dd).length === 0 ? '' : this.getEvent(dd)[0].event_name,
               dt: d + ' ' + this.getDay(d),
               // day: this.getDay(d)
             }
             
             this.calendar.push(event)
           }
        },
        getDay (d) {
            var dt = new Date();
            var month = dt.getMonth();
            var year = dt.getFullYear();

            var n = new Date(year, month, d).getDay();
            var day = this.eventForm.days[n].text;
            return day;
        },
       formatDate (date) {
          return (date.getFullYear() + '-' +
              ('0' + (date.getMonth() + 1)).slice(-2) + '-' +
              ('0' + date.getDate()).slice(-2))
        },
        getEvent (date) {
           return this.events.filter((e) => {
               return (this.formatDate(new Date(e.event_date)) === this.formatDate(new Date(date)))
             })
        },
        getColor (eventName) {
        if (eventName !== '') return 'info'
        else return 'transparent'
      },
    }
  }
</script>