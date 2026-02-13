import './bootstrap'

// FullCalendar (ESM)
import { Calendar } from '@fullcalendar/core'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'

// โยนไว้ให้ใช้ใน Blade ได้
window.FC = { Calendar, dayGridPlugin, timeGridPlugin, interactionPlugin }
