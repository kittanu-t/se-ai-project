import { Calendar } from '@fullcalendar/core'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'


function pad(n){ return String(n).padStart(2,'0') }

function initMiniCalendar() {
  const calEl = document.getElementById('mini-calendar')
  if (!calEl) return

  const fieldSelect = document.getElementById('sports_field_id')
  const dateInput   = document.getElementById('date')

  const calendar = new Calendar(calEl, {
    plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
    initialView: 'timeGridWeek',
    height: 400,
    nowIndicator: true,
    allDaySlot: false,
    slotMinTime: '06:00:00',
    slotMaxTime: '23:00:00',
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek,timeGridDay'
    },
    initialDate: dateInput?.value || undefined,
    events: (info, ok, fail) => {
      const fieldId = fieldSelect?.value
      if (!fieldId) { ok([]); return }
      const url = `/api/fields/${fieldId}/events?start=${encodeURIComponent(info.start.toISOString())}&end=${encodeURIComponent(info.end.toISOString())}`
      fetch(url, { credentials: 'same-origin' })
        .then(r => r.json())
        .then(data => ok(data))
        .catch(fail)
    }
  })

  calendar.render()
}

document.addEventListener('DOMContentLoaded', initMiniCalendar)
